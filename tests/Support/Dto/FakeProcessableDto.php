<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Dto;

use IBroStudio\Tasks\Dto\ProcessableDto;

class FakeProcessableDto extends ProcessableDto
{
    public function __construct(
        public string $name,
    ) {}
}
