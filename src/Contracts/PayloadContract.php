<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

interface PayloadContract
{
    public function updateDto(array $data): self;

    public function toArray(): array;
}
