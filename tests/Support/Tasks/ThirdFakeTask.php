<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Tasks;

use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Database\Factories\ThirdFakeTaskFactory;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class ThirdFakeTask extends Task
{
    use HasFactory;
    use HasParent;

    public function handle(FakeProcess|ProcessContract $process): FakeProcess|ProcessContract
    {
        return $process;
    }

    protected static function newFactory(): Factory
    {
        return ThirdFakeTaskFactory::new();
    }
}
