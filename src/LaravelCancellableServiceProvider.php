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
    public function boot()
    {
        $this->configureMacros();
        $this->configureDirectives();
    }

    /**
     * Configure the macros to be used.
     *
     * @return void
     */
    protected function configureMacros()
    {
        Blueprint::macro('cancelledAt', function ($column = 'cancelled_at', $precision = 0) {
            return $this->timestamp($column, $precision)->nullable();
        });
    }

    /**
     * Configure the macros to be used.
     *
     * @return void
     */
    protected function configureDirectives()
    {
        Blade::directive('ifCancelled', static function ($object) {
            return "<?php if ($object->isCancelled($object)) : ?>";
        });
        Blade::directive('endifCancelled', static function () {
            return '<?php endif; ?>';
        });
        Blade::directive('ifnotCancelled', static function ($object) {
            return "<?php if (!$object->isCancelled($object)) : ?>";
        });
        Blade::directive('endifnotCancelled', static function () {
            return '<?php endif; ?>';
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
