<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Actions;

use IBroStudio\Tasks\Models\Process;
use Lorisleiva\Actions\Concerns\AsAction;

class AsyncProcessAction
{
    use AsAction;

    public function handle(Process $process): void
    {
        $process->handle();
    }
}
