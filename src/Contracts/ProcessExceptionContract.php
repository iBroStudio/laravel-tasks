<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;

/**
 * @property Process $process
 * @property Task $task
 */
interface ProcessExceptionContract
{
    public Process $process { get; }

    public Task $task { get; }
}
