<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

it('can create tasks', function () {
    $process = FakeProcess::factory()->create();

    expect($process->tasks->first())->toBeInstanceOf(FakeFirstTask::class)
        ->and($process->tasks->last())->toBeInstanceOf(IBroStudio\Tasks\Tests\Support\Tasks\ThirdFakeTask::class);
});

it('can run a task as standalone', function () {
    $task = FakeFirstTask::factory()->create(['process_id' => null]);

    $task->handle(FakePayloadDefault::from(['property1' => 'value1']));

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

it('can run tasks', function () {
    $process = FakeProcess::factory()->create()->handle();

    expect($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('can skip a task', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1', 'skip_task' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::SKIPPED)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::COMPLETED);
});
