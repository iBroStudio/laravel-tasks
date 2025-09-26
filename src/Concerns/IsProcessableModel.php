<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Dto\DefaultProcessPayloadDto;
use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Bus\PendingDispatch;

trait IsProcessableModel
{
    /**
     * @param  class-string<Process>  $processClass
     * @return ($async is true ? PendingDispatch : Process)
     */
    public static function callProcess(
        string $processClass,
        PayloadContract|array $payload,
        ?string $monitoring_uuid = null,
        bool $async = false): Process|PendingDispatch
    {
        return (new static)->process($processClass, $payload, $monitoring_uuid, $async);
    }

    /**
     * @param  class-string<Task>  $taskClass
     * @return ($async is true ? PendingDispatch : Task)
     */
    public static function callTask(
        string $taskClass,
        PayloadContract $payload,
        bool $async = false): Task|PendingDispatch
    {
        return (new static)->task($taskClass, $payload, $async);
    }

    public function processes(): MorphMany
    {
        return $this->morphMany(Process::class, 'processable');
    }

    /**
     * @param  class-string<Process>  $processClass
     */
    public function process(
        string $processClass,
        PayloadContract|array|null $payload = null,
        ?string $monitoring_uuid = null,
        bool $async = false): Process
    {
        $process = $this->processes()
            ->create([
                'type' => $processClass,
                'payload' => $payload,
                'monitoring_uuid' => $monitoring_uuid,
            ]);

        if (! $async) {
            return $process->handle();
        }

        $process->dispatch();

        return $process;
    }

    /**
     * @param  class-string<Process>  $processClass
     */
    public function dispatch(
        string $processClass,
        PayloadContract|array|null $payload = null,
        ?string $monitoring_uuid = null): Process
    {
        return $this->process(
            $processClass,
            $payload,
            $monitoring_uuid,
            async: true
        );
    }

    /**
     * @param  class-string<Task>  $taskClass
     */
    public function task(
        string $taskClass,
        ?PayloadContract $payload = null,
        bool $async = false): Task
    {
        $payload = $payload ?? DefaultProcessPayloadDto::from();

        return $this->tasks()
            ->create(['type' => $taskClass])
            ->tap(fn (Task $task) => $task->when($async, fn (Task $task) => $task->dispatch($payload))
                && $task->unless($async, fn (Task $task) => $task->handle($payload))
            );
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'processable');
    }
}
