<?php

namespace LaravelCancellable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelCancellable\LaravelCancellableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'LaravelCancellable\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory';
            }
        );
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('cancellable_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('cancelled_at', 0)->nullable();
        });

        Schema::create('regular_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCancellableServiceProvider::class,
        ];
    }
}
