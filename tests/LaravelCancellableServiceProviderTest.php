<?php

namespace LaravelCancellable\Tests;

use Illuminate\Database\Schema\Blueprint;
use LaravelCancellable\LaravelCancellableServiceProvider;
use LaravelCancellable\Tests\TestCase;

class LaravelCancellableServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_the_macros(): void
    {
        $provider = new LaravelCancellableServiceProvider($this->app);

        $provider->boot();

        $this->assertTrue(Blueprint::hasMacro('cancelledAt'));
    }
}
