<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\DTO;

use IBroStudio\DataObjects\Concerns\UpdatableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use Spatie\LaravelData\Data;

class DefaultProcessPayloadDTO extends Data implements PayloadContract
{
    use UpdatableDto;
}
