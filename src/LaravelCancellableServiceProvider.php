<?php

namespace LaravelCancellable;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LaravelCancellableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureMacros();
    }

    /**
     * Configure the macros to be used.
     *
     * @return void
     */
    protected function configureMacros(): void
    {
        Blueprint::macro('cancelledAt', function ($column = 'cancelled_at', $precision = 0) {
            return $this->timestamp($column, $precision)->nullable();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
