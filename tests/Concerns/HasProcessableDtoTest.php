<?php

declare(strict_types=1);

use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

test('process can have a processable DTO', function () {
    $process = FakeProcess::factory()->withProcessableDto()->create();

    expect($process->processable_dto)->toBeInstanceOf(ProcessableDto::class);
});

test('task can have a processable DTO', function () {
    $task = FakeFirstTask::factory()->withProcessableDto()->create();

    expect($task->processable_dto)->toBeInstanceOf(ProcessableDto::class);
});
