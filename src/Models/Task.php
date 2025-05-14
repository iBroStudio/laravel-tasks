<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Models;

use Closure;
use IBroStudio\Tasks\Concerns\HasProcessableModel;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Exceptions\SkipTaskException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Traits\Tappable;
use Lorisleiva\Actions\Concerns\AsObject;
use Parental\HasChildren;
use Spatie\LaravelData\Data;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @property int $id
 * @property string $type
 * @property Data $payload
 * @property TaskStatesEnum $state
 * @property int $process_id
 * @property Process $process
 */
class Task extends Model
{
    use AsObject;
    use HasChildren;
    use HasFactory;
    use HasProcessableModel;
    use Tappable;

    public $timestamps = false;

    protected $table = 'processes_tasks';

    protected $fillable = [
        'type',
        'process_id',
        'state',
        'as_process_id',
        'processable_id',
        'processable_type',
        'processable_dto',
    ];

    protected static function booted(): void
    {
        static::created(function (Task $task) {
            if (! is_null($task->process) && ! is_null($task->process->processable)) {
                $task->process->processable->tasks()->save($task);
            }
        });
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function asProcess(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'as_process_id');
    }

    public function handle(PayloadContract $payload): PayloadContract|array
    {
        $this->transitionTo(TaskStatesEnum::STARTED);

        return tap($this->execute($payload),
            fn (PayloadContract|array $process) => $this->transitionTo(TaskStatesEnum::COMPLETED)
        );
    }

    public function asProcessTask(ProcessContract $process, Closure $next): mixed
    {
        try {
            $payload = $this->handle($process->payload);

            return $next($process->updatePayload($payload));

        } catch (SkipTaskException $skipTask) {

            return $next($process);
        }
    }

    public function transitionTo(TaskStatesEnum $state, ?string $message = null): void
    {
        $this->state = $state;

        $this->save();

        $this->process?->log(task: $this, message: $message);
    }

    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        return $payload;
    }

    protected function casts(): array
    {
        return [
            'state' => TaskStatesEnum::class,
        ];
    }
}
