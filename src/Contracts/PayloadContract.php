<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

interface PayloadContract
{
    public function update(array $data): self;
}
