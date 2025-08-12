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
        bool $async = false): Process|PendingDispatch
    {
        return (new static)->process($processClass, $payload, $async);
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
        bool $async = false): Process
    {
        return $this->processes()
            ->create([
                'type' => $processClass,
                'payload' => $payload,
            ])
            ->tap(fn (Process $process) =>
                $process->when($async, fn (Process $process) => $process->dispatch())
                && $process->unless($async, fn (Process $process) => $process->handle())
            );

        return $this->processes()
            ->create([
                'type' => $processClass,
                'payload' => $payload,
            ])
            ->handle();
    }

    /**
     * @param  class-string<Process>  $processClass
     */
    public function dispatch(string $processClass, PayloadContract|array|null $payload = null): Process
    {
        return $this->process(
            $processClass,
            $payload,
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
            ->tap(fn (Task $task) =>
                $task->when($async, fn (Task $task) => $task->dispatch($payload))
                && $task->unless($async, fn (Task $task) => $task->handle($payload))
            );
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'processable');
    }
}
