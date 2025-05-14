<?php

declare(strict_types=1);

use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Tests\Support\DTO\ProcessableDto;
use IBroStudio\Tasks\Tests\Support\Processes\FakeProcess;

it('allows processable DTO to call process', function () {
    $processable = ProcessableDto::from([
        'name' => fake()->name,
    ]);
    $process = $processable->process(FakeProcess::class, ['property1' => 'value1']);

    expect($process)->toBeInstanceOf(FakeProcess::class)
        ->and($process->processable_dto)->toBeInstanceOf(ProcessableDto::class)
        ->and($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});
