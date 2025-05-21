<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Tests\Support\Dto\FakeProcessableDto;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeParentProcess;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

it('allows processable DTO to call process', function () {
    $processable = FakeProcessableDto::from([
        'name' => fake()->name,
    ]);
    $process = $processable->process(FakeProcess::class, FakePayloadDefault::from(['property1' => 'value1']));

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->processable_dto)->toBeInstanceOf(FakeProcessableDto::class)
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks->first()->processable_dto)->toBeInstanceOf(FakeProcessableDto::class)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('allows processable DTO to call task', function () {
    $processable = FakeProcessableDto::from([
        'name' => fake()->name,
    ]);
    $task = $processable->task(FakeFirstTask::class, FakePayloadDefault::from(['property1' => 'value1']));

    expect($task)->toBeInstanceOf(FakeFirstTask::class)
        ->and($task->processable_dto)->toBeInstanceOf(FakeProcessableDto::class)
        ->and($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

it('can execute a process within a process with a processable DTO', function () {
    $processable = FakeProcessableDto::from([
        'name' => fake()->name,
    ]);
    $process = $processable->process(FakeParentProcess::class, FakePayloadDefault::from(['property1' => 'value1']));

    $child_process = Process::whereType(FakeProcess::class)->first();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED))
        ->and($child_process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($child_process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});
