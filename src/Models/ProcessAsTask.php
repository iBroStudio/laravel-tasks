<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Models;

use IBroStudio\Tasks\Contracts\PayloadContract;
use Parental\HasParent;

class ProcessAsTask extends Task
{
    use HasParent;

    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        $process = $this->asProcess->handle($payload);

        return $process->payload;
    }
}
