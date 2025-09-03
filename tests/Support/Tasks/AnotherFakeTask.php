<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Tasks;

use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Exceptions\PauseProcessException;
use IBroStudio\Tasks\Exceptions\SkipTaskException;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Database\Factories\AnotherFakeTaskFactory;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class AnotherFakeTask extends Task
{
    use HasFactory;
    use HasParent;

    protected static function newFactory(): Factory
    {
        return AnotherFakeTaskFactory::new();
    }

    /**
     * @param  FakePayloadDefault  $payload
     */
    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        if ($payload->skip_task) {
            throw new SkipTaskException($this, 'This is the skip message.');
        }

        if ($payload->abort_process) {
            throw new AbortTaskAndProcessException($this, 'This is the abortion message.');
        }

        if ($payload->pause_process) {
            $this->process->updatePayload(['pause_process' => false]);
            throw new PauseProcessException($this, 'This is the pause message.');
        }

        return [
            'property2' => 'value3',
            'modelproperty' => FakeProcess::factory()->create([
                'payload' => FakePayloadDefault::from(['property1' => 'test']),
            ]),
        ];
    }
}
