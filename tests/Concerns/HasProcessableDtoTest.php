<?php

declare(strict_types=1);

use IBroStudio\Tasks\Tests\Support\Dto\FakeProcessableDto;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;
use IBroStudio\Tasks\Tests\Support\Tasks\ThirdFakeTask;

test('process can have a processable DTO', function () {
    $process = FakeProcess::factory()->withProcessableDto()->create();

    expect($process->processable_dto)->toBeInstanceOf(FakeProcessableDto::class);
});

test('task can have a processable DTO', function () {
    $task = FakeFirstTask::factory()->withProcessableDto()->create();

    expect($task->processable_dto)->toBeInstanceOf(FakeProcessableDto::class);
});

test('task can have a processable DTO d', function () {
    $task = ThirdFakeTask::factory()->withProcessableDto()->create();

    expect($task->processable_dto)->toBeInstanceOf(FakeProcessableDto::class);
});
