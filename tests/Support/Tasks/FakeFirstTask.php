<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Tasks;

use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Tests\Support\Database\Factories\FakeFirstTaskFactory;
use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class FakeFirstTask extends Task
{
    use HasFactory;
    use HasParent;
    use HasProcessableDto;

    protected static function newFactory(): Factory
    {
        return FakeFirstTaskFactory::new();
    }

    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        return ['property1' => 'value2'];
    }

    protected function getProcessableDtoClass(): string
    {
        return ProcessableDto::class;
    }
}
