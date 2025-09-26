<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Models;

use IBroStudio\DataObjects\Concerns\HasConfig;
use IBroStudio\DataObjects\ValueObjects\ClassString;
use IBroStudio\Tasks\Actions\AsyncProcessAction;
use IBroStudio\Tasks\Actions\Logs\EnsureProcessLogPerformedOnAction;
use IBroStudio\Tasks\Concerns\CanBeResumed;
use IBroStudio\Tasks\Concerns\HasHandleExtractor;
use IBroStudio\Tasks\Concerns\HasLogs;
use IBroStudio\Tasks\Concerns\HasProcessableModel;
use IBroStudio\Tasks\Concerns\HasTasks;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Contracts\ProcessExceptionContract;
use IBroStudio\Tasks\Dto\DefaultProcessPayloadDto;
use IBroStudio\Tasks\Dto\ProcessConfigDto;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Traits\Tappable;
use Lorisleiva\Actions\Concerns\AsObject;
use Parental\HasChildren;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\LaravelData\Data;

/**
 * @property-read int $id
 * @property class-string $type
 * @property PayloadContract $payload
 * @property ProcessStatesEnum $state
 * @property string $log_batch_uuid
 * @property int $parent_process_id
 * @property-read ProcessConfigDto $config
 * @property-read \Illuminate\Database\Eloquent\Collection|Model[] $processable
 * @property-read Process|null $parentProcess
 */
class Process extends Model implements ProcessContract
{
    use AsObject;
    use CanBeResumed;
    use HasChildren;
    use HasConfig;
    use HasFactory;
    use HasHandleExtractor;
    use HasLogs;
    use HasProcessableModel;
    use HasTasks;
    use Tappable;

    protected $table = 'processes';

    protected $fillable = [
        'type',
        'payload',
        'state',
        'parent_process_id',
        'log_batch_uuid',
        'processable_id',
        'processable_type',
        'processable_dto',
        'monitoring_uuid',
    ];

    protected $with = ['tasks'];

    public function parentProcess(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_process_id');
    }

    public function handle(): self
    {
        try {

            $this->transitionToStart();

            return tap(
                Pipeline::send($this)
                    ->via('asProcessTask')
                    ->through($this->processableTasks()->get()->all())
                    ->then(function (Process $process) {
                        return $process->refresh();
                    }),
                fn (Process $process) => $this->transitionToComplete()
            );

        } catch (ProcessExceptionContract $processException) {

            if ($this->parent_process_id) {

                $class = get_class($processException);

                throw new $class(
                    Task::whereAsProcessId($this->id)->first(),
                    $processException->getMessage(),
                );
            }

            return $processException->task->process->refresh();
        }
    }

    public function dispatch(): void
    {
        AsyncProcessAction::dispatch($this);
    }

    public function isSuccessful(): bool
    {
        return $this->state === ProcessStatesEnum::COMPLETED;
    }

    public function transitionToStart(): void
    {
        if ($this->config->use_logs) {
            LogBatch::startBatch();
            $this->log_batch_uuid = LogBatch::getUuid();
        }

        $this->transitionTo(ProcessStatesEnum::PROCESSING);
    }

    public function transitionTo(ProcessStatesEnum $state, ?string $message = null): void
    {
        $this->state = $state;

        $this->save();

        $this->log(message: $message);
    }

    public function transitionToComplete(?ProcessStatesEnum $state = null, ?string $message = null): void
    {
        $this->update(['state' => $state ?? ProcessStatesEnum::COMPLETED]);

        if ($this->config->use_logs) {
            $this->log(message: $message);
            LogBatch::endBatch();
            EnsureProcessLogPerformedOnAction::dispatch($this);
        }
    }

    public function updatePayload(PayloadContract|array $data): self
    {
        $payload = is_array($data) ? $this->payload->updateDto($data) : $data;

        /** @var Data $payload */
        if ($this->payload->equalTo($payload)) {
            return $this;
        }

        $this->payload = $payload;

        return $this->tap()->save();
    }

    protected static function booted(): void
    {
        static::creating(function (Process $process) {
            if (is_null($process->payload)) {
                $process->payload = with($process->getConfig()->payload, fn ($payload) => $payload instanceof ClassString ?
                    $payload->value::from() : DefaultProcessPayloadDto::from()
                );
            }
        });
    }

    protected function casts()
    {
        return [
            'state' => ProcessStatesEnum::class,
            'payload' => DefaultProcessPayloadDto::class,
        ];
    }

    protected function getConfig(array $properties = []): ProcessConfigDto
    {
        return ProcessConfigDto::from([
            'tasks' => [],
            'log_name' => static::extractHandleFromClassName(),
            ...$properties,
        ]);
    }
}
