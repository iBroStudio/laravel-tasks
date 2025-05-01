<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Events\TaskExecutionCompletedEvent;
use IBroStudio\Tasks\Events\TaskExecutionStartedEvent;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;
use Illuminate\Support\Facades\Event;

it('can create tasks', function () {
    $process = FakeProcess::factory()->create();

    expect($process->tasks->first())->toBeInstanceOf(FakeFirstTask::class)
        ->and($process->tasks->last())->toBeInstanceOf(IBroStudio\Tasks\Tests\Support\Tasks\ThirdFakeTask::class);
});

it('can run tasks', function () {
    $process = FakeProcess::factory()->create()->handle();

    expect($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});
/*
it('can handle run task events', function () {
    Event::fake([
        TaskExecutionStartedEvent::class,
        TaskExecutionCompletedEvent::class,
    ]);

    FakeProcess::factory()->create()->handle();

    Event::assertDispatched(TaskExecutionStartedEvent::class, 3);

    Event::assertDispatched(TaskExecutionCompletedEvent::class, 3);
});
*/
it('can skip a task', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'skip_task' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::SKIPPED)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::COMPLETED);
});
