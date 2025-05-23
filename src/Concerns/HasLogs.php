<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Actions\Logs\LogEventAction;
use IBroStudio\Tasks\Dto\ProcessLogDto;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @see \IBroStudio\Tasks\Models\Process
 */
trait HasLogs
{
    protected Collection $parsedProcessClass;

    protected Collection $parsedTaskClass;

    public function log(?Task $task = null, ?string $message = null): void
    {
        if ($this->config->use_logs) {
            $this->parsedProcessClass = $this->parseTypeClass($this->type);
            ! is_null($task) && $this->parsedTaskClass = $this->parseTypeClass($task->type);

            LogEventAction::dispatch(
                $this->log_batch_uuid,
                new ProcessLogDto(
                    logName: $this->logName(),
                    causedBy: auth()->user(),
                    performedOn: $this->processable ?? $this->parentProcess ?? $this,
                    event: $this->state,
                    description: $message ?? $this->logDescription($task),
                    properties: $this->payload->toArray(),
                )
            );
        }
    }

    public function logName(): string
    {
        if (! is_null($this->parentProcess)) {
            return $this->parentProcess->config->log_name;
        }

        return $this->config->log_name;
    }

    public function getLogNameFromClass(?string $string = null): string
    {
        return $this->parseTypeClass($string ?? get_class($this))
            ->implode('-');
    }

    protected function logDescription(?Task $task = null): string
    {
        return (
            $task instanceof Task ?
                $this->parsedTaskClass->push($task->state->getLabel())
                : $this->parsedProcessClass->push($this->state->getLabel())
        )
            ->implode(' ');
    }

    protected function parseTypeClass(string $string): Collection
    {
        return Str::of($string)
            ->classBasename()
            ->chopEnd('Process')
            ->chopEnd('Task')
            ->split('/(?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z])/', -1, PREG_SPLIT_NO_EMPTY)
            ->map(function (string $item) {
                return Str::lower($item);
            });
    }
}
