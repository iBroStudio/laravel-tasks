<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

/**
 * @see \IBroStudio\Tasks\Models\Process
 */
trait HasProcessableDto
{
    abstract protected function getProcessableDtoClass(): string;

    public function initializeHasProcessableDto(): void
    {
        $this->mergeCasts([
            'processable_dto' => $this->getProcessableDtoClass(),
        ]);
    }
}
