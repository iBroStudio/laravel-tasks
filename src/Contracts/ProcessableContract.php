<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Contracts;

use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Bus\PendingDispatch;

interface ProcessableContract
{
    /**
     * @param  class-string<Process>  $processClass
     * @return ($async is true ? PendingDispatch : Process)
     */
    public static function callProcess(
        string $processClass,
        PayloadContract|array $payload,
        ?string $monitoring_uuid = null,
        bool $async = false): Process|PendingDispatch;

    /**
     * @param  class-string<Task>  $taskClass
     * @return ($async is true ? PendingDispatch : Task)
     */
    public static function callTask(
        string $taskClass,
        PayloadContract $payload,
        bool $async = false): Task|PendingDispatch;

    public function processes(): MorphMany;

    /**
     * @param  class-string<Process>  $processClass
     */
    public function process(
        string $processClass,
        PayloadContract|array|null $payload = null,
        ?string $monitoring_uuid = null,
        bool $async = false): Process;

    /**
     * @param  class-string<Process>  $processClass
     */
    public function dispatch(
        string $processClass,
        PayloadContract|array|null $payload = null,
        ?string $monitoring_uuid = null): Process;

    /**
     * @param  class-string<Task>  $taskClass
     */
    public function task(
        string $taskClass,
        ?PayloadContract $payload = null,
        bool $async = false): Task;

    public function tasks(): MorphMany;
}
