<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Tasks;

use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Database\Factories\ThirdFakeTaskFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class ThirdFakeTask extends Task
{
    use HasFactory;
    use HasParent;

    protected static function newFactory(): Factory
    {
        return ThirdFakeTaskFactory::new();
    }

    protected function execute(PayloadContract $payload): PayloadContract
    {
        return $payload;
    }
}
