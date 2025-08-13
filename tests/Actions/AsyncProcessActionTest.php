<?php

declare(strict_types=1);

use IBroStudio\Tasks\Actions\AsyncProcessAction;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

it('can run an async process', function () {
    Queue::fake();

    $process = FakeProcess::factory()->create();

    AsyncProcessAction::dispatch($process);

    AsyncProcessAction::assertPushed();
});
