<?php

namespace LaravelCancellable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use LaravelCancellable\LaravelCancellableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static function (string $modelName) {
                return 'LaravelCancellable\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
            }
        );
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('cancellable_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('cancelled_at', 0)->nullable();
        });

        Schema::create('regular_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelCancellableServiceProvider::class,
        ];
    }
}
