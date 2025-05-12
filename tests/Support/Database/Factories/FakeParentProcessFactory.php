<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeParentProcess;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakeParentProcessFactory extends Factory
{
    protected $model = FakeParentProcess::class;

    public function definition()
    {
        return [
            'payload' => FakePayload::from(['property1' => 'value1']),
            'state' => ProcessStatesEnum::PENDING,
        ];
    }
}
