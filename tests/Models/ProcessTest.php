<?php

declare(strict_types=1);

use IBroStudio\DataObjects\ValueObjects\Text;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Events\ProcessExecutionCompletedEvent;
use IBroStudio\Tasks\Events\ProcessExecutionStartedEvent;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Illuminate\Support\Facades\Event;

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
        ->and($process->payload->property2->value)->toBe('value3')
        ->and($process->payload->modelproperty)->toBeInstanceOf(FakeProcess::class);
});
/*
it('can handle run process events', function () {
    Queue::fake();
    Event::fake([
        ProcessExecutionStartedEvent::class,
        ProcessExecutionCompletedEvent::class,
    ]);

    $process = FakeProcess::factory()->create()->handle();

    Event::assertDispatched(ProcessExecutionStartedEvent::class, function (ProcessExecutionStartedEvent $event) use ($process) {
        return $event->process->is($process);
    });

    Event::assertDispatched(ProcessExecutionCompletedEvent::class, function (ProcessExecutionCompletedEvent $event) use ($process) {
        return $event->process->is($process);
    });
});
*/
it('can abort a process', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'abort_process' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::ABORTED)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::ABORTED)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::PENDING);
});

it('can pause a process', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'pause_process' => true]),
    ])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::WAITING)
        ->and($process->tasks->first()->state)->toBe(TaskStatesEnum::COMPLETED)
        ->and($process->tasks->get(1)->state)->toBe(TaskStatesEnum::WAITING)
        ->and($process->tasks->last()->state)->toBe(TaskStatesEnum::PENDING);
});

it('can resume a process from a signed url', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'pause_process' => true]),
    ])->handle();

    //    dd($process->tasks->toArray());

    get($process->resumeUrl())->assertSuccessful();

    $process->refresh();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});
