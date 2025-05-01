<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Tasks;

use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Exceptions\PauseProcessException;
use IBroStudio\Tasks\Exceptions\SkipTaskException;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Database\Factories\AnotherFakeTaskFactory;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class AnotherFakeTask extends Task
{
    use HasFactory;
    use HasParent;

    /**
     * @param  FakeProcess  $process
     */
    public function handle(FakeProcess|ProcessContract $process): FakeProcess|ProcessContract
    {
        if ($process->payload->skip_task) {
            throw new SkipTaskException($process, $this);
        }

        if ($process->payload->abort_process) {
            throw new AbortTaskAndProcessException($process, $this);
        }

        if ($process->payload->pause_process) {
            $process->updatePayload(['pause_process' => false]);
            throw new PauseProcessException($process, $this);
        }

        return $process->updatePayload([
            'property2' => 'value3',
            'modelproperty' => FakeProcess::factory()->create([
                'payload' => FakePayload::from(['property1' => 'test']),
            ]),
        ]);
    }

    protected static function newFactory(): Factory
    {
        return AnotherFakeTaskFactory::new();
    }
}
