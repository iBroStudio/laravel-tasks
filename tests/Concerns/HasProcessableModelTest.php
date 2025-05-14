<?php

declare(strict_types=1);

use IBroStudio\Tasks\Tests\Support\Models\ProcessableFakeModel;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

it('can have a processable', function () {
    $processable = ProcessableFakeModel::factory()->create();
    $process = FakeProcess::factory()->create();

    expect($process->addProcessable($processable))->toBeTrue()
        ->and($process->processable->is($processable))->toBeTrue();
});
