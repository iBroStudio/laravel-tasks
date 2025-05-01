<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Models;

use Closure;
use IBroStudio\Tasks\Contracts\ProcessContract;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Exceptions\SkipTaskException;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;
use Parental\HasChildren;
use Spatie\LaravelData\Data;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @property int $id
 * @property string $type
 * @property Data $payload
 * @property TaskStatesEnum $state
 * @property int $process_id
 * @property Process $process
 */
class Task extends Model
{
    use AsAction;
    use HasChildren;
    use HasFactory;

    public $timestamps = false;

    protected $table = 'processes_tasks';

    protected $fillable = [
        'type',
        'process_id',
        'state',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function handle(ProcessContract $process): ProcessContract
    {
        return $process;
    }

    public function asProcessTask(ProcessContract $process, Closure $next): mixed
    {
        try {
            $this->transitionTo(TaskStatesEnum::STARTED);

            return $next(tap($this->handle($process),
                fn (ProcessContract $process) => $this->transitionTo(TaskStatesEnum::COMPLETED)
            ));

        } catch (SkipTaskException $skipTask) {

            return $next($skipTask->process);
        }
    }

    public function transitionTo(TaskStatesEnum $state): void
    {
        $this->state = $state;

        $this->save();

        $this->process->log($this);
    }

    protected function casts(): array
    {
        return [
            'state' => TaskStatesEnum::class,
        ];
    }

    #[Scope]
    protected function processable(Builder $query): void
    {
        $query->whereIn('state', [
            TaskStatesEnum::WAITING,
            TaskStatesEnum::PENDING,
        ]);
    }
}
