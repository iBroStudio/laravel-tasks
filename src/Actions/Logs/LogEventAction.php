<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Actions\Logs;

use IBroStudio\Tasks\Dto\ProcessLogDto;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Facades\LogBatch;

class LogEventAction
{
    use AsAction;

    public function handle(string $log_batch_uuid, ProcessLogDto $logData): void
    {
        LogBatch::startBatch();
        LogBatch::setBatch($log_batch_uuid);

        activity($logData->logName)
            ->causedBy($logData->causedBy)
            ->performedOn($logData->performedOn['type']::find($logData->performedOn['id']))
            ->event($logData->event->getLabel())
            ->withProperties($logData->properties)
            ->log($logData->description);
    }
}
