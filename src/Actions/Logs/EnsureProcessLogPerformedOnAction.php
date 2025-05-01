<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Actions\Logs;

use IBroStudio\Tasks\Models\Process;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

class EnsureProcessLogPerformedOnAction
{
    use AsAction;

    public function handle(Process $process): void
    {
        // @phpstan-ignore-next-line
        if (! is_null($process->processable)) {
            Activity::where('batch_uuid', $process->log_batch_uuid)
                ->where('subject_type', '<>', $process->processable::class)
                ->update([
                    // @phpstan-ignore-next-line
                    'subject_id' => $process->processable->id,
                    'subject_type' => get_class($process->processable),
                ]);
        }
    }
}
