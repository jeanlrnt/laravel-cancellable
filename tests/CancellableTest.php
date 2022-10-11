<?php

namespace LaravelCancellable\Tests;

use LaravelCancellable\Tests\TestClasses\CancellableModel;
use LaravelCancellable\Tests\TestClasses\RegularModel;

class CancellableTest extends TestCase
{
    /** @test */
    public function a_model_can_be_cancelled()
    {
        $model = CancellableModel::factory()->create();

        $this->assertNull($model->fresh()->cancelled_at);

        $model->cancel();

        $this->assertNotNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_uncancelled()
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertNotNull($model->fresh()->cancelled_at);

        $model->uncancel();

        $this->assertNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_cannot_be_queried_normally_when_cancelled()
    {
        CancellableModel::factory()->cancelled()->create();

        CancellableModel::factory()->create();

        $this->assertDatabaseCount('cancellable_models', 2);

        $this->assertCount(1, CancellableModel::all());
    }

    /** @test */
    public function all_models_can_be_found_with_the_withCancelled_scope()
    {
        CancellableModel::factory()->cancelled()->create();
        CancellableModel::factory()->create();

        $this->assertCount(2, CancellableModel::withCancelled()->get());
    }

    /** @test */
    public function only_cancelled_models_can_be_found_with_the_onlyCancelled_scope()
    {
        CancellableModel::factory()->cancelled()->create();
        CancellableModel::factory()->create();

        $this->assertCount(1, CancellableModel::onlyCancelled()->get());
    }

    /** @test */
    public function models_without_the_cancellable_trait_are_not_scoped()
    {
        RegularModel::factory()->create();

        $this->assertCount(1, RegularModel::all());
    }
}
