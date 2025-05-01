<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Actions\Logs;

use IBroStudio\Tasks\DTO\ProcessLogDTO;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;

class LogEventAction
{
    use AsAction;

    public function handle(string $log_batch_uuid, ProcessLogDTO $logData): void
    {
        LogBatch::startBatch();
        LogBatch::setBatch($log_batch_uuid);

        activity($logData->logName)
            ->causedBy($logData->causedBy)
            ->tap(function (Activity $activity) use ($logData) {
                // @phpstan-ignore-next-line
                $activity->subject_id = $logData->performedOn->id;
                // @phpstan-ignore-next-line
                $activity->subject_type = $logData->performedOn->type;
            })
            ->event($logData->event->getLabel())
            ->withProperties($logData->properties)
            ->log($logData->description);
    }
}
