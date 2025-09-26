<?php

declare(strict_types=1);

use IBroStudio\Tasks\Tests\Support\Tasks\FakeFirstTask;

it('has a monitorin config', function () {

    expect(
        FakeFirstTask::getMonitoringConfig()
    )->toMatchArray([
        'fake-first' => [
            'title' => 'fake first',
            'state' => 'waiting',
        ],
    ]);
});
