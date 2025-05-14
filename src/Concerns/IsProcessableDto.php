<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Models\Process;

trait IsProcessableDto
{
    /**
     * @param  class-string<Process>  $processClass
     */
    public function process(
        string $processClass,
        array $payload_properties = [],
        bool $async = false): Process
    {
        return $processClass::create([
            'payload' => $payload_properties,
            'processable_dto' => $this,
        ])->handle();
    }
}
