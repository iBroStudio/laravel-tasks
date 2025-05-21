<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Dto;

use IBroStudio\DataObjects\Concerns\UpdatableDto;
use IBroStudio\Tasks\Contracts\PayloadContract;
use Spatie\LaravelData\Data;

abstract class DefaultProcessPayloadDto extends Data implements PayloadContract
{
    use UpdatableDto;
}
