<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Database\Factories;

use IBroStudio\Tasks\Tests\Support\Models\ProcessableFakeModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProcessableFakeModelFactory extends Factory
{
    protected $model = ProcessableFakeModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
