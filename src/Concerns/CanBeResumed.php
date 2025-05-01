<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use Illuminate\Support\Facades\URL;

/**
 * @see \IBroStudio\Tasks\Models\Process
 */
trait CanBeResumed
{
    public static function resume(int $process_id): self
    {
        return self::whereId($process_id)
            ->whereState(ProcessStatesEnum::WAITING)
            ->firstOrFail()
            ->handle();
    }

    public function resumeUrl(): string
    {
        return URL::signedRoute('tasks-process', [
            'process_id' => $this,
            'batch' => '',
        ]);
    }
}
