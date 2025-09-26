<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Dto;

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class ProcessLogDto extends Data
{
    public function __construct(
        public string $logName,
        public Model|string|null $causedBy,
        public array $performedOn,
        public ProcessStatesEnum|TaskStatesEnum $event,
        public string $description,
        public array $properties = [],
    ) {}
}
