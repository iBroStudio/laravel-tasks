<?php

declare(strict_types=1);

use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

it('can have a processable DTO', function () {
    $process = FakeProcess::factory()->withProcessableDto()->create();

    expect($process->processable_dto)->toBeInstanceOf(ProcessableDto::class);
});
