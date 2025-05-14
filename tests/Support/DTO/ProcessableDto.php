<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\DTO;

use IBroStudio\Tasks\Concerns\IsProcessableDto;
use Spatie\LaravelData\Data;

class ProcessableDto extends Data
{
    use IsProcessableDto;

    public function __construct(
        public string $name,
    ) {}
}
