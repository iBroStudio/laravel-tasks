<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\AnotherFakeTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnotherFakeTaskFactory extends Factory
{
    protected $model = AnotherFakeTask::class;

    public function definition()
    {
        return [
            'process_id' => FakeProcess::factory([
                'payload' => FakePayloadDefault::from(['property1' => 'value1']),
            ]),
        ];
    }
}
