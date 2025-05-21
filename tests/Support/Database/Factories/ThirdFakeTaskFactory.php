<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Tests\Support\Dto\FakeProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
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
                'payload' => FakePayloadDefault::from(['property1' => 'value1']),
            ]),
        ];
    }

    public function withProcessableDto(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'processable_dto' => FakeProcessableDto::from([
                    'name' => fake()->name,
                ]),
            ];
        });
    }
}
