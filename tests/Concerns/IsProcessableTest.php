<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\Models\ProcessableFakeModel;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

use function Pest\Laravel\assertModelExists;

it('can have processes', function () {
    $processable = ProcessableFakeModel::factory()->create();

    $processable->processes()->create([
        'type' => FakeProcess::class,
        'payload' => FakePayload::from(['property1' => 'value1']),
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
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));

});

it('allows processable class to call statically process', function () {
    $process = ProcessableFakeModel::callProcess(FakeProcess::class, ['property1' => 'value1']);

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});
