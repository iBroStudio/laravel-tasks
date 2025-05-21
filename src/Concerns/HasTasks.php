<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\DataObjects\ValueObjects\ClassString;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\ProcessAsTask;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Processes\FakeParentProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @see Process
 */
trait HasTasks
{
    public static function bootHasTasks()
    {
        static::created(function (Process $process) {

            $process->tasks()->createMany(
                $process->config->tasks->map(function (ClassString $type) use ($process) {

                    $task_properties = ['type' => $type->value];

                    if (is_a($type->value, Process::class, true)) {
                        $child_process = $type->value::create([
                            'payload' => $process->payload,
                            'processable_dto' => $process->processable_dto,
                        ]);

                        $task_properties = ['type' => ProcessAsTask::class, 'as_process_id' => $child_process->id];
                    }

                    if (method_exists($type->value, 'getProcessableDtoClass') && ! is_null($process->processable_dto)) {
                        data_set($task_properties, 'processable_dto', $process->processable_dto);
                    }

                    return $task_properties;
                })->toArray(),
            );
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'process_id');
    }

    public function task(string $type): ?Model
    {
        return $this->tasks()->where('type', $type)->first();
    }

    public function currentTask(): ?Model
    {
        return $this->tasks()->where('state', ProcessStatesEnum::PROCESSING)->first();
    }

    public function waitingTask(): ?Model
    {
        return $this->tasks()->where('state', ProcessStatesEnum::WAITING)->first();
    }

    public function processableTasks(): HasMany
    {
        return $this->tasks()->whereIn('state', [
            TaskStatesEnum::WAITING,
            TaskStatesEnum::PENDING,
        ]);
    }
}
