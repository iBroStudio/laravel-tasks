<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Concerns;

use IBroStudio\Tasks\Models\Process;
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
        array $payload_properties = [],
        bool $async = false): Process|PendingDispatch
    {
        return (new static)->process($processClass, $payload_properties, $async);
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
        array $payload_properties = [],
        bool $async = false): Process
    {
        return $processClass::create(['payload' => $payload_properties])->handle();
    }
}
