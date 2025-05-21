<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Processes;

use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Dto\ProcessConfigDto;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Tests\Support\Database\Factories\FakeParentProcessFactory;
use IBroStudio\Tasks\Tests\Support\Dto\FakeProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;
use IBroStudio\Tasks\Tests\Support\Tasks\ThirdFakeTask;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

/**
 * @property int $id
 * @property string $class
 * @property FakePayloadDefault $payload
 * @property ProcessStatesEnum $state
 * @property string $log_batch_uuid
 * @property int $parent_process_id
 */
class FakeParentProcess extends Process implements ProcessContract
{
    use HasFactory;
    use HasParent;
    use HasProcessableDto;

    protected $table = 'processes';

    public function getProcessableDtoClass(): string
    {
        return FakeProcessableDto::class;
    }

    protected static function newFactory(): Factory
    {
        return FakeParentProcessFactory::new();
    }

    protected function getConfig(array $properties = []): ProcessConfigDto
    {
        return parent::getConfig([
            'tasks' => [
                FakeFirstTask::class,
                FakeProcess::class,
                ThirdFakeTask::class,
            ],
        ]);
    }
}
