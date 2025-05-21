<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\DTO;

use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Spatie\LaravelData\Data;

abstract class ProcessableDto extends Data
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

    /**
     * @param  class-string<Task>  $taskClass
     */
    public function task(
        string $taskClass,
        PayloadContract $payload,
        bool $async = false): Task
    {
        return $taskClass::create(['processable_dto' => $this])
            ->tap()
            ->handle($payload);
    }
}
