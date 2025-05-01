<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\ThirdFakeTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThirdFakeTaskFactory extends Factory
{
    protected $model = ThirdFakeTask::class;

    public function definition()
    {
        return [
            'process_id' => FakeProcess::factory([
                'payload' => FakePayload::from(['property1' => 'value1']),
            ]),
        ];
    }
}
