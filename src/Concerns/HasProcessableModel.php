<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @see \IBroStudio\Tasks\Models\Process
 * @see \IBroStudio\Tasks\Models\Task
 */
trait HasProcessableModel
{
    public function processable(): MorphTo
    {
        return $this->morphTo();
    }

    public function addProcessable(Model $processable): bool
    {
        return $this->processable()
            ->associate($processable)
            ->save();
    }
}
