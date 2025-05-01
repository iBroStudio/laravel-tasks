<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\DTO;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class ProcessLogDTO extends Data
{
    public function __construct(
        public string $logName,
        public Model|string|null $causedBy,
        public Model $performedOn,
        public ProcessStatesEnum $event,
        public string $description,
        public array $properties = [],
    ) {}
}
