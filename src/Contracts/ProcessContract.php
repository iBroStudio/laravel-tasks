<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;

/**
 * @property-read int $id
 * @property string $class
 * @property-read PayloadContract $payload
 * @property-read ProcessStatesEnum $state
 * @property string $log_batch_uuid
 * @property int $parent_process_id
 * @property-read ProcessConfigDTO $config
 */
interface ProcessContract
{
    public function updatePayload(array $data): self;
}
