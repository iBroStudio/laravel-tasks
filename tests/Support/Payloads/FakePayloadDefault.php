<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Tests\Support\Payloads;

use IBroStudio\DataObjects\ValueObjects\Text;
use IBroStudio\Tasks\Dto\DefaultProcessPayloadDto;
use IBroStudio\Tasks\Models\Process;
use Spatie\LaravelData\Optional;

class FakePayloadDefault extends DefaultProcessPayloadDto
{
    public function __construct(
        public string $property1,
        public Text|Optional $property2,
        public Process|array|Optional $modelproperty,
        public bool $skip_task = false,
        public bool $abort_process = false,
        public bool $pause_process = false,
    ) {}
}
