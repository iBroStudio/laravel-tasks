<?php

namespace IBroStudio\Tasks\Tests;

use IBroStudio\DataObjects\DataObjectsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use IBroStudio\Tasks\TasksServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IBroStudio\\Tasks\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->artisan('vendor:publish', [
            '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
            '--tag' => 'activitylog-migrations',
        ])->run();

        $this->artisan('migrate')->run();
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

         foreach (File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }

        foreach (File::allFiles(__DIR__ . '/Support/Database/Migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            TasksServiceProvider::class,
            DataObjectsServiceProvider::class,
            LaravelDataServiceProvider::class,
            ActivitylogServiceProvider::class,
        ];
    }
}
