<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\DataObjects\ValueObjects\ClassString;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTasks
{
    public static function bootHasTasks()
    {
        static::created(function (Process $process) {
            $process->tasks()->createMany(
                $process->config->tasks->map(function (ClassString $type) {
                    return ['type' => $type->value];
                })->toArray(),
            );
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'process_id');
    }

    public function task(string $type): Task
    {
        return $this->tasks()->where('type', $type)->first();
    }

    public function currentTask(): Task
    {
        return $this->tasks()->where('state', ProcessStatesEnum::PROCESSING)->first();
    }

    public function waitingTask(): Task
    {
        return $this->tasks()->where('state', ProcessStatesEnum::WAITING)->first();
    }

    public function processableTasks(): HasMany
    {
        return $this->tasks()->processable();
    }
}
