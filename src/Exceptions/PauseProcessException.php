<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Exceptions;

use Exception;
use IBroStudio\Tasks\Contracts\ProcessExceptionContract;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Models\Task;

class PauseProcessException extends Exception implements ProcessExceptionContract
{
    public function __construct(public Task $task, protected $message = null)
    {
        $task->transitionTo(state: TaskStatesEnum::WAITING, message: $message);

        if ($this->task->process) {
            $this->task->process->transitionToComplete(state: ProcessStatesEnum::WAITING, message: $message);
        }

        parent::__construct($message ?? '');
    }
}
