<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Broadcast;

/**
 * @see \IBroStudio\Tasks\Models\Task
 */
trait Monitorable
{
    public static function getMonitoringConfig(): array
    {
        return [
            static::extractHandleFromClassName() => [
                'title' => __(static::extractHandleFromClassName(' ')),
                'state' => TaskStatesEnum::WAITING->value,
            ],
        ];
    }

    public function broadcast(): void
    {
        if (! is_null($this->process) && auth()->check()) {

            Broadcast::private('App.Models.User.'.auth()->user()->id)
                ->as('UpdateProcessTask')
                ->with([
                    'id' => $this->process->monitoring_uuid ?? $this->process->id,
                    'handle' => static::extractHandleFromClassName(),
                    'state' => $this->state,
                ])
                ->sendNow();

            sleep(1);
        }
    }
}
