<?php

declare(strict_types=1);

use IBroStudio\DataObjects\ValueObjects\Text;
use IBroStudio\Tasks\Actions\AsyncProcessAction;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayloadDefault;
use IBroStudio\Tasks\Tests\Support\Processes\FakeParentProcess;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\get;

it('can create a process', function () {
    $process = FakeProcess::factory()->create();

    assertModelExists($process);
});

it('can run a process', function () {
    $process = FakeProcess::factory()->create()->handle();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->payload->property1)->toBe('value2')
        ->and($process->payload->property2)->toBeInstanceOf(Text::class)
        ->and($process->payload->property2->value)->toBe('value3');
});

it('can abort a process', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1', 'abort_process' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::ABORTED)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::ABORTED)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::PENDING);
});

it('can pause a process', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1', 'pause_process' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::WAITING)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::WAITING)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::PENDING);
});

it('can resume a process from a signed url', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1', 'pause_process' => true]),
    ])->handle();

    //    dd($process->tasks->toArray());

    get($process->resumeUrl())->assertSuccessful();

    $process->refresh();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('can update a payload', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1', 'pause_process' => true]),
    ]);

    expect(
        $process->updatePayload(['property1' => 'value2'])
            ->payload
            ->property1
    )->toBe('value2');
});

it('can execute a process within a process', function () {
    $process = FakeParentProcess::factory()->create([
        'payload' => FakePayloadDefault::from(['property1' => 'value1']),
    ])->handle();

    $child_process = Process::whereType(FakeProcess::class)->first();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED))
        ->and($child_process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($child_process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

it('can dispatch a process', function () {
    Queue::fake();

    FakeProcess::factory()->create()->dispatch();

    AsyncProcessAction::assertPushed();
});
