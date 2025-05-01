<?php

declare(strict_types=1);

namespace IBroStudio\Tasks;

use IBroStudio\Tasks\Commands\TasksCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TasksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-tasks')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_tasks_table')
            ->hasCommand(TasksCommand::class)
            ->hasRoute('web');
    }
}
