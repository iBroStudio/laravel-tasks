<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Tests\Support\Dto\FakeProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakeProcessFactory extends Factory
{
    protected $model = FakeProcess::class;

    public function definition()
    {
        return [
            'payload' => FakePayloadDefault::from(['property1' => 'value1']),
            'state' => ProcessStatesEnum::PENDING,
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
