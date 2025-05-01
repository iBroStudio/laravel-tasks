<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Models;

use IBroStudio\DataObjects\Concerns\HasConfig;
use IBroStudio\Tasks\Actions\Logs\EnsureProcessLogPerformedOnAction;
use IBroStudio\Tasks\Concerns\CanBeResumed;
use IBroStudio\Tasks\Concerns\HasLogs;
use IBroStudio\Tasks\Concerns\HasProcessable;
use IBroStudio\Tasks\Concerns\HasTasks;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Contracts\ProcessExceptionContract;
use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\DTO\ProcessPayloadDTO;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Traits\Tappable;
use Lorisleiva\Actions\Concerns\AsAction;
use Parental\HasChildren;
use Spatie\Activitylog\Facades\LogBatch;

/**
 * @property-read int $id
 * @property string $class
 * @property PayloadContract $payload
 * @property ProcessStatesEnum $state
 * @property string $log_batch_uuid
 * @property int $parent_process_id
 * @property-read ProcessConfigDTO $config
 */
class Process extends Model implements ProcessContract
{
    use AsAction;
    use CanBeResumed;
    use HasChildren;
    use HasConfig;
    use HasFactory;
    use HasLogs;
    use HasProcessable;
    use HasTasks;
    use Tappable;

    protected $fillable = [
        'type',
        'payload',
        'state',
        'parent_process_id',
        'log_batch_uuid',
        'processable',
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
                    ->thenReturn(),
                fn (Process $process) => $this->transitionToComplete()
            );

        } catch (ProcessExceptionContract $processException) {

            return $processException->process;

        }
    }

    public function transitionToStart(): void
    {
        if ($this->config->use_logs) {
            LogBatch::startBatch();
            $this->log_batch_uuid = LogBatch::getUuid();
        }

        $this->transitionTo(ProcessStatesEnum::PROCESSING);
    }

    public function transitionTo(ProcessStatesEnum $state): void
    {
        $this->state = $state;

        $this->save();

        $this->log();
    }

    public function transitionToComplete(?ProcessStatesEnum $state = null): void
    {
        $this->update(['state' => $state ?? ProcessStatesEnum::COMPLETED]);

        if ($this->config->use_logs) {
            $this->log();
            LogBatch::endBatch();
            EnsureProcessLogPerformedOnAction::dispatch($this);
        }
    }

    public function updatePayload(array $data): self
    {
        return $this->tap()->update([
            'payload' => $this->payload->update($data),
        ]);
    }

    protected function casts()
    {
        return [
            'state' => ProcessStatesEnum::class,
        ];
    }

    protected function getConfig(array $properties = []): ProcessConfigDTO
    {
        return ProcessConfigDTO::from([
            'payload' => ProcessPayloadDTO::class,
            'tasks' => [],
            'log_name' => $this->getLogNameFromClass(),
            ...$properties,
        ]);
    }
}
