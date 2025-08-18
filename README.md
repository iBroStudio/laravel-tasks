# Laravel Tasks

A comprehensive Laravel package for managing pipelined processes and their tasks with state management, logging, and async execution capabilities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ibrostudio/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/ibrostudio/laravel-tasks)
[![Total Downloads](https://img.shields.io/packagist/dt/ibrostudio/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/ibrostudio/laravel-tasks)

## Features

- **Pipeline-based Process Execution**: Execute complex workflows using Laravel's Pipeline pattern
- **State Management**: Track process and task states (PENDING, PROCESSING, COMPLETED, ABORTED, WAITING)
- **Task Orchestration**: Chain multiple tasks within processes with automatic state transitions
- **Pause & Resume**: Pause processes and resume them via signed URLs
- **Nested Processes**: Execute processes within other processes for complex workflows
- **Async Execution**: Dispatch processes to queues for background processing
- **Comprehensive Logging**: Built-in activity logging with batch support
- **Polymorphic Relations**: Associate processes with any Eloquent model
- **Payload Management**: Type-safe payload handling with DTOs
- **Exception Handling**: Graceful error handling with task-specific exceptions

## Installation

Install the package via Composer:

```bash
composer require ibrostudio/laravel-tasks
```

Run the installation command:

```bash
php artisan tasks:install
```

This command will:
- Publish the configuration file
- Publish and run the migrations

## Configuration

The package publishes a configuration file to `config/tasks.php`:

```php
<?php

return [
    'log_processes' => true,  // Enable/disable process logging
    'queue' => 'processes',   // Queue name for async process execution
];
```

## Database Schema

The package creates two main tables:

### `processes` table
- `id`: Primary key
- `type`: Process class name
- `payload`: JSON payload data
- `state`: Current process state
- `processable_id/processable_type`: Polymorphic relation
- `processable_dto`: JSON DTO data
- `parent_process_id`: For nested processes
- `log_batch_uuid`: Activity log batch UUID
- `created_at/updated_at`: Timestamps

### `processes_tasks` table
- `id`: Primary key
- `process_id`: Foreign key to processes
- `type`: Task class name
- `as_process_id`: For tasks that run as sub-processes
- `state`: Current task state
- `processable_id/processable_type`: Polymorphic relation
- `processable_dto`: JSON DTO data

## Basic Usage

### Creating a Process

Create a process class by extending the base `Process` model:

```php
<?php

namespace App\Processes;

use IBroStudio\Tasks\Models\Process;
use IBroStudio\Tasks\Dto\ProcessConfigDto;
use App\Tasks\ValidateDataTask;
use App\Tasks\ProcessDataTask;
use App\Tasks\SendNotificationTask;

class DataProcessingProcess extends Process
{
    protected function getConfig(array $properties = []): ProcessConfigDto
    {
        return ProcessConfigDto::from([
            'tasks' => [
                ValidateDataTask::class,
                ProcessDataTask::class,
                SendNotificationTask::class,
            ],
            'use_logs' => true,
            'log_name' => 'data-processing',
            ...$properties,
        ]);
    }
}
```

### Creating Tasks

Create task classes by extending the base `Task` model:

```php
<?php

namespace App\Tasks;

use IBroStudio\Tasks\Models\Task;
use IBroStudio\Tasks\Contracts\PayloadContract;

class ValidateDataTask extends Task
{
    /**
     * @param  MyCustomPaylodDto  $payload
     */
    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        // Perform task logic
        
        // Return updated payload
        return $payload->updateDto(['validated' => true]);
    }
}
```

### Creating Custom Payloads

Create type-safe payload DTOs:

```php
<?php

namespace App\Dto;

use IBroStudio\Tasks\Dto\DefaultProcessPayloadDto;

class DataProcessingPayloadDto extends DefaultProcessPayloadDto
{
    public function __construct(
        public array $data,
        public bool $validated = false,
        public bool $processed = false,
        public ?string $notification_sent = null,
    ) {}
}
```
Payloads DTOs extend Spatie [Laravel Data](https://github.com/spatie/laravel-data).
### Executing Processes

#### Synchronous Execution

```php
use App\Processes\DataProcessingProcess;
use App\Dto\DataProcessingPayloadDto;

// Create and execute a process
$process = DataProcessingProcess::->create([
    'payload' => DataProcessingPayloadDto::from([
        'data' => ['user_id' => 123, 'required_field' => 'value']
    ])
]);

$result = $process->handle();

// Check the result
if ($result->state === ProcessStatesEnum::COMPLETED) {
    echo "Process completed successfully!";
    echo "Final payload: " . json_encode($result->payload);
}
```

#### Asynchronous Execution

```php
// Dispatch to queue
$process->dispatch();

// The process will be executed in the background
// You can check the status later
$process->refresh();
echo "Current state: " . $process->state->value;
```

### Working with Processable Models

Associate processes with Eloquent models:

```php
use App\Models\User;
use IBroStudio\Tasks\Concerns\IsProcessableModel;

class User extends Model
{
    use IsProcessableModel;
    
    // optional
    public function processUserData(): Process
    {
        return $this->processes()->create([
            'type' => DataProcessingProcess::class,
            'payload' => DataProcessingPayloadDto::from([
                'data' => $this->toArray()
            ])
        ]);
    }
}

// Usage
$user = User::find(1);
$process = $user->process(DataProcessingProcess::class, DataProcessingPayloadDto::from(['user_id' => 123, 'required_field' => 'value']));
// or
$user->dispatch(DataProcessingProcess::class, DataProcessingPayloadDto::from(['user_id' => 123, 'required_field' => 'value']));
// or
$process = $user->processUserData()->handle();
```

## Advanced Features

### Pausing and Resuming Processes

Tasks can pause processes and generate signed URLs for resumption:

```php
use IBroStudio\Tasks\Exceptions\PauseProcessException;

class RequireApprovalTask extends Task
{
    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        if ($this->requiresApproval($payload)) {
            // This will pause the process and generate a resume URL
            throw new PauseProcessException(
                task: $this,
                message: 'Approval required'
            );
        }
        
        return $payload;
    }
    
    private function requiresApproval(PayloadContract $payload): bool
    {
        return $payload->amount > 10000;
    }
}

// Generate resume URL
$resumeUrl = $process->resumeUrl();

// Send URL to approver via email/notification
Mail::to($approver)->send(new ApprovalRequired($resumeUrl));
```

### Nested Processes

Execute processes within other processes:

```php
class ParentProcess extends Process
{
    protected function getConfig(array $properties = []): ProcessConfigDto
    {
        return ProcessConfigDto::from([
            'tasks' => [
                PrepareDataTask::class,
                ExecuteChildProcess::class, // This task will run a sub-process
                FinalizeTask::class,
            ],
            ...$properties,
        ]);
    }
}
```

### Exception Handling

Handle different types of exceptions:

```php
use IBroStudio\Tasks\Exceptions\AbortProcessException;
use IBroStudio\Tasks\Exceptions\SkipTaskException;

class ConditionalTask extends Task
{
    protected function execute(PayloadContract $payload): PayloadContract|array
    {
        if ($payload->should_abort) {
            throw new AbortProcessException(
                task: $this,
                message: 'Process aborted due to condition'
            );
        }
        
        if ($payload->should_skip) {
            throw new SkipTaskException('Skipping this task');
        }
        
        // Normal execution
        return $payload->updateDto(['processed' => true]);
    }
}
```

### Custom Process Configuration

Override configuration for specific needs:

```php
class CustomProcess extends Process
{
    protected function getConfig(array $properties = []): ProcessConfigDto
    {
        return ProcessConfigDto::from([
            'tasks' => [
                CustomTask::class,
            ],
            'use_logs' => false,           // Disable logging
            'payload' => CustomPayload::class, // Custom payload class
            'queue' => 'high-priority',    // Custom queue
            ...$properties,
        ]);
    }
}
```

## Testing

The package is thoroughly tested with Pest. Run the tests:

```bash
composer test
```

### Testing Your Processes

```php
use function Pest\Laravel\assertModelExists;

it('can execute a data processing process', function () {
    $process = DataProcessingProcess::factory()->create([
        'payload' => DataProcessingPayloadDto::from([
            'data' => ['required_field' => 'test_value']
        ])
    ]);
    
    $result = $process->handle();
    
    expect($result->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($result->tasks)->each(fn (Task $task) => $task->state->toBe(TaskStatesEnum::COMPLETED))
        ->and($result->payload->validated)->toBeTrue()
        ->and($result->payload->processed)->toBeTrue();
        
    assertModelExists($result);
});

it('can pause and resume a process', function () {
    Queue::fake();
    
    $process = ApprovalProcess::factory()->create([
        'payload' => ApprovalPayloadDto::from([
            'amount' => 15000 // This will trigger pause
        ])
    ]);
    
    $result = $process->handle();
    
    expect($result->state)->toBe(ProcessStatesEnum::WAITING);
    
    // Simulate approval via resume URL
    get($result->resumeUrl())->assertSuccessful();
    
    $result->refresh();
    expect($result->state)->toBe(ProcessStatesEnum::COMPLETED);
});
```

## API Reference

### Process Methods

- `handle()`: Execute the process synchronously
- `dispatch()`: Queue the process for async execution
- `transitionTo(ProcessStatesEnum $state, ?string $message = null)`: Change process state
- `updatePayload(PayloadContract|array $data)`: Update the process payload
- `resumeUrl()`: Generate signed URL for resuming paused processes

### Task Methods

- `handle(PayloadContract $payload)`: Execute the task
- `execute(PayloadContract $payload)`: Override this method for custom task logic
- `transitionTo(TaskStatesEnum $state, ?string $message = null)`: Change task state

### Available States

#### ProcessStatesEnum
- `PENDING`: Process created but not started
- `PROCESSING`: Process is currently running
- `COMPLETED`: Process finished successfully
- `ABORTED`: Process was aborted due to error
- `WAITING`: Process is paused and waiting for external action

#### TaskStatesEnum
- `PENDING`: Task not yet started
- `STARTED`: Task is currently executing
- `COMPLETED`: Task finished successfully
- `ABORTED`: Task was aborted
- `WAITING`: Task is paused

### Available Exceptions

- `AbortProcessException`: Aborts the entire process
- `PauseProcessException`: Pauses the process for external action
- `SkipTaskException`: Skips the current task and continues

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Check code style: `composer pint`

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [iBroStudio](https://github.com/iBroStudio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
