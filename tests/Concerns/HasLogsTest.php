<?php

declare(strict_types=1);

use IBroStudio\Tasks\Actions\Logs\EnsureProcessLogPerformedOnAction;
use IBroStudio\Tasks\Actions\Logs\LogEventAction;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\Payloads\FakePayload;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;

it('can dispatch log action', function () {
    Queue::fake();
    $process = FakeProcess::factory()->create()->handle();

    LogEventAction::assertPushed(8);

    EnsureProcessLogPerformedOnAction::assertPushed(function ($action, $arguments) use ($process) {
        return $arguments[0]->is($process);
    });
});

it('can log process events', function () {
    $process = FakeProcess::factory()->create()->handle();
    $logs = Activity::inLog($process->logName())->get();

    // dd($logs->toArray());
    expect(
        $logs->get(0)->except('created_at', 'updated_at')
    )->toMatchArray([
        'id' => 1,
        'log_name' => 'fake',
        'description' => 'fake processing',
        'subject_type' => FakeProcess::class,
        'subject_id' => 1,
        'causer_type' => null,
        'causer_id' => null,
        'properties' => collect([
            'property1' => 'value1',
            'skip_task' => false,
            'abort_process' => false,
            'pause_process' => false,
        ]),
        'event' => ProcessStatesEnum::PROCESSING->getLabel(),
        'batch_uuid' => $process->log_batch_uuid,
    ])
        ->and($logs->get(1)->event)->toBe(TaskStatesEnum::PROCESSING->getLabel())
        ->and($logs->get(1)->description)->toBe('fake first started')
        ->and($logs->last()->event)->toBe(ProcessStatesEnum::COMPLETED->getLabel())
        ->and($logs->last()->description)->toBe('fake completed')
        ->and(LogBatch::isOpen())->toBeFalse();
});

it('can log a skip message', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'skip_task' => true]),
    ])->handle();
    $logs = Activity::inLog($process->logName())->get();

    expect($logs->get(4)->description)->toBe('This is the skip message.');
});

it('can log a pause message', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'pause_process' => true]),
    ])->handle();
    $logs = Activity::inLog($process->logName())->get();

    expect($logs->get(4)->description)->toBe('This is the pause message.')
        ->and($logs->get(5)->description)->toBe('This is the pause message.');
});

it('can log an abortion message', function () {
    $process = FakeProcess::factory()->create([
        'payload' => FakePayload::from(['property1' => 'value1', 'abort_process' => true]),
    ])->handle();
    $logs = Activity::inLog($process->logName())->get();

    expect($logs->get(4)->description)->toBe('This is the abortion message.')
        ->and($logs->get(5)->description)->toBe('This is the abortion message.');
});
