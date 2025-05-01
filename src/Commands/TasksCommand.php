<?php

declare(strict_types=1);

namespace IBroStudio\Tasks\Commands;

use Illuminate\Console\Command;

class TasksCommand extends Command
{
    public $signature = 'laravel-tasks';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
