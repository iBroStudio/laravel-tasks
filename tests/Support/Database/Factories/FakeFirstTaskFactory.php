<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakeFirstTaskFactory extends Factory
{
    protected $model = FakeFirstTask::class;

    public function definition()
    {
        return [
            'process_id' => FakeProcess::factory([
                'payload' => FakePayload::from(['property1' => 'value1']),
            ]),
        ];
    }

    public function withProcessableDto(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'processable_dto' => ProcessableDto::from([
                    'name' => fake()->name,
                ]),
            ];
        });
    }
}
