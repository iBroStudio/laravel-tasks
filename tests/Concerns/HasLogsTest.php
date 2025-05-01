<?php

declare(strict_types=1);

use IBroStudio\Tasks\Actions\Logs\EnsureProcessLogPerformedOnAction;
use IBroStudio\Tasks\Actions\Logs\LogEventAction;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;

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
        ->and($logs->get(1)->event)->toBe(ProcessStatesEnum::PROCESSING->getLabel())
        ->and($logs->get(1)->description)->toBe('fake first started')
        ->and($logs->get(7)->event)->toBe(ProcessStatesEnum::COMPLETED->getLabel())
        ->and($logs->get(7)->description)->toBe('fake completed')
        ->and(LogBatch::isOpen())->toBeFalse();
});
