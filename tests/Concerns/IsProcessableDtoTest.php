<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

it('allows processable DTO to call process', function () {
    $processable = ProcessableDto::from([
        'name' => fake()->name,
    ]);
    $process = $processable->process(FakeProcess::class, ['property1' => 'value1']);

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->processable_dto)->toBeInstanceOf(ProcessableDto::class)
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks->first()->processable_dto)->toBeInstanceOf(ProcessableDto::class)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('allows processable DTO to call task', function () {
    $processable = ProcessableDto::from([
        'name' => fake()->name,
    ]);
    $task = $processable->task(FakeFirstTask::class, FakePayload::from(['property1' => 'value1']));

    expect($task)->toBeInstanceOf(FakeFirstTask::class)
        ->and($task->processable_dto)->toBeInstanceOf(ProcessableDto::class)
        ->and($task->state)->toBe(TaskStatesEnum::COMPLETED);
});
