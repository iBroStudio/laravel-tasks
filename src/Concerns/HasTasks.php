<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\DataObjects\ValueObjects\ClassString;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @see \IBroStudio\Tasks\Models\Process
 */
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
        // @phpstan-ignore-next-line
        return $this->tasks()->processable();
    }
}
