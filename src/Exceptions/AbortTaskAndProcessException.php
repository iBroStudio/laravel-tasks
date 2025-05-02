<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Exceptions;

use Exception;
use IBroStudio\Tasks\Contracts\ProcessExceptionContract;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;

class AbortTaskAndProcessException extends Exception implements ProcessExceptionContract
{
    public function __construct(public Process $process, public Task $task, protected $message = null)
    {
        $task->transitionTo(state: TaskStatesEnum::ABORTED, message: $message);
        $process->transitionToComplete(state: ProcessStatesEnum::ABORTED, message: $message);

        parent::__construct($message ?? '');
    }
}
