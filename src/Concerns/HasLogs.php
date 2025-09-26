<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Actions\Logs\LogEventAction;
use IBroStudio\Tasks\Dto\ProcessLogDto;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Support\Facades\Broadcast;
use Spatie\Activitylog\Models\Activity;

/**
 * @see \IBroStudio\Tasks\Models\Process
 */
trait HasLogs
{
    public function log(?Task $task = null, ?string $message = null): void
    {
        if ($this->config->use_logs) {

            $performedOn = $this->processable ?? $this->parentProcess ?? $this;

            LogEventAction::dispatch(
                $this->log_batch_uuid,
                new ProcessLogDto(
                    logName: $this->logName(),
                    causedBy: auth()->user(),
                    performedOn: [
                        'id' => $performedOn->id,
                        'type' => get_class($performedOn),
                    ],
                    event: $this->state,
                    description: $message ?? $this->logDescription($task),
                    properties: $this->payload->toArray(),
                )
            );

            /*
            Broadcast::private('App.Models.User.'.auth()->user()->id)
                ->as('UpdateNotificationBody')
                ->with([
                    'id' => 'notifId',
                    'body' => $message ?? $this->logDescription($task),
                ])
                ->sendNow();

            sleep(1);
            */
        }
    }

    public function logName(): string
    {
        if (! is_null($this->parentProcess)) {
            return $this->parentProcess->config->log_name;
        }

        return $this->config->log_name;
    }

    public function getLastLoggedMessage(): string
    {
        return Activity::query()
            ->where('log_name', $this->logName())
            ->get()
            ->last()
            ->description;
    }

    protected function logDescription(?Task $task = null): string
    {
        if ($task instanceof Task) {
            return $task::extractHandleFromClassName(
                delimiter: ' ',
                append: $task->state->getLabel()
            );
        }

        return static::extractHandleFromClassName(
            delimiter: ' ',
            append: $this->state->getLabel()
        );
    }
}
