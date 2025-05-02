<?php

declare(strict_types=1);

namespace IBroStudio\Tasks;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasRoute('web')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('iBroStudio/laravel-tasks');
            });
    }
}
