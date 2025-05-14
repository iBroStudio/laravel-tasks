<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakeProcessFactory extends Factory
{
    protected $model = FakeProcess::class;

    public function definition()
    {
        return [
            'payload' => FakePayload::from(['property1' => 'value1']),
            'state' => ProcessStatesEnum::PENDING,
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
