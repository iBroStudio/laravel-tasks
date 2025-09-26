<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/**
 * @see \IBroStudio\Tasks\Models\Process
 * @see \IBroStudio\Tasks\Models\Task
 */
trait HasHandleExtractor
{
    public static function extractHandleFromClassName($delimiter = '-', ?string $append = null): string
    {
        return Str::of(static::class)
            ->classBasename()
            ->chopEnd(['Process', 'Task'])
            ->snake($delimiter)
            ->when(! is_null($append), fn (Stringable $string) => $string
                ->append($delimiter)
                ->append($append)
            )
            ->value();
    }
}
