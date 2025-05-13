<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\DTO;

use IBroStudio\Tasks\Contracts\PayloadContract;
use Spatie\LaravelData\Data;

class ProcessPayloadDTO extends Data implements PayloadContract
{
    public function update(array $data): self
    {
        return self::from([...$this->all(), ...$data]);
    }
}
