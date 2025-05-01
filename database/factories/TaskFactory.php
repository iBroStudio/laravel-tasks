<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Database\Factories;

use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'process_id' => Process::factory(),
        ];
    }
}
