<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\Models\ProcessableFakeModel;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

use function Pest\Laravel\assertModelExists;

it('can have process', function () {
    $processable = ProcessableFakeModel::factory()->create();

    $processable->processes()->create([
        'type' => FakeProcess::class,
        'payload' => FakePayloadDefault::from(['property1' => 'value1']),
    ]);

    assertModelExists(
        $processable->processes->first()
    );
});

it('can attach a process', function () {
    $processable = ProcessableFakeModel::factory()->create();
    $process = FakeProcess::factory()->create();
    $processable->processes()->save($process);

    expect($processable->processes->first()->is($process))->toBeTrue();
});

it('allows processable to call process', function () {
    $processable = ProcessableFakeModel::factory()->create();
    $process = $processable->process(FakeProcess::class, ['property1' => 'value1']);

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->processable->is($processable))->toBeTrue()
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks->first()->processable->is($processable))->toBeTrue()
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('allows processable class to call process statically', function () {
    $process = ProcessableFakeModel::callProcess(FakeProcess::class, ['property1' => 'value1']);

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('can have task', function () {
    $processable = ProcessableFakeModel::factory()->create();

    $processable->tasks()->create(['type' => FakeFirstTask::class]);

    assertModelExists(
        $processable->tasks->first()
    );
});

it('can attach a task', function () {
    $processable = ProcessableFakeModel::factory()->create();
    $task = FakeFirstTask::factory()->create();
    $processable->tasks()->save($task);

    expect($processable->tasks->first()->is($task))->toBeTrue();
});

it('allows processable to call task', function () {
    $processable = ProcessableFakeModel::factory()->create();
    $task = $processable->task(FakeFirstTask::class, FakePayloadDefault::from(['property1' => 'value1']));

    expect($task)->toBeInstanceOf(FakeFirstTask::class)
        ->and($task->processable->is($processable))->toBeTrue()
        ->and($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

it('allows processable class to call task statically', function () {
    $task = ProcessableFakeModel::callTask(FakeFirstTask::class, FakePayloadDefault::from(['property1' => 'value1']));

    expect($task)->toBeInstanceOf(FakeFirstTask::class)
        ->and($task->state)->toBe(TaskStatesEnum::COMPLETED);
});
