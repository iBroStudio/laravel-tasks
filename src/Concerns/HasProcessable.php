<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Contracts\ProcessableContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait HasProcessable
{
    public function processable(): MorphTo
    {
        return $this->morphTo();
    }

    public function addProcessable(ProcessableContract $processable): bool
    {
        return $this->processable()
            ->associate($processable)
            ->save();
    }
}
