<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @see \IBroStudio\Tasks\Models\Process
 * @see Task
 */
trait HasProcessableModel
{
    public function processable(): MorphTo
    {
        return $this->morphTo();
    }

    public function addProcessable(Model $processable): bool
    {
        if (method_exists($this, 'tasks')) {
            $this->tasks->each(function (Task $task) use ($processable) {
                $task->addProcessable($processable);
                $task->asProcess?->addProcessable($processable);
            });
        }

        return $this->processable()
            ->associate($processable)
            ->save();
    }
}
