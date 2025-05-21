<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Dto;

use IBroStudio\DataObjects\Attributes\EloquentCast;
use IBroStudio\DataObjects\DTO\ModelConfigDTO;
use IBroStudio\DataObjects\ValueObjects\ClassString;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Optional;

class ProcessConfigDto extends ModelConfigDTO
{
    public function __construct(
        #[EloquentCast]
        public ClassString|Optional $payload,
        /** @var Collection<int, ClassString> */
        public Collection $tasks,
        public string|Optional $log_name,
        public bool $use_logs = true
    ) {}
}
