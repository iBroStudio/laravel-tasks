<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

use IBroStudio\Tasks\Models\Task;

/**
 * @property Task $task
 */
interface ProcessExceptionContract
{
    public Task $task { get; }
}
