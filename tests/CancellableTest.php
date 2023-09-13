<?php

namespace LaravelCancellable\Tests;

use Carbon\Carbon;
use LaravelCancellable\Tests\TestClasses\CancellableModel;
use LaravelCancellable\Tests\TestClasses\RegularModel;

class CancellableTest extends TestCase
{
    /** @test */
    public function a_model_can_be_cancelled(): void
    {
        $model = CancellableModel::factory()->create();

        $this->assertNull($model->fresh()->cancelled_at);

        $model->cancel();

        $this->assertNotNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_create(): void
    {
        $model = CancellableModel::forceCreate(['cancelled_at' => now()]);

        $this->assertNotNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_save(): void
    {
        $model = CancellableModel::factory()->create();

        $this->assertNull($model->cancelled_at);

        $model->forceFill(['cancelled_at' => now()]);

        $model->save();

        $this->assertNotNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_update(): void
    {
        $model = CancellableModel::factory()->create();

        $this->assertNull($model->cancelled_at);

        $model->forceFill(['cancelled_at' => now()]);

        $model->update();

        $this->assertNotNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_create_many(): void
    {
        $models = CancellableModel::factory()->count(2)->make();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => now()])->save();
        });

        $this->assertCount(2, $models->fresh()->whereNotNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_save_many(): void
    {
        $models = CancellableModel::factory()->count(2)->create();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => now()])->save();
        });

        $this->assertCount(2, $models->fresh()->whereNotNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_cancelled_on_force_update_many(): void
    {
        $models = CancellableModel::factory()->count(2)->create();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => now()])->update();
        });

        $this->assertCount(2, $models->fresh()->whereNotNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_uncancelled(): void
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertNotNull($model->fresh()->cancelled_at);

        $model->uncancel();

        $this->assertNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_create(): void
    {
        $model = CancellableModel::forceCreate(['cancelled_at' => null]);

        $this->assertNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_save(): void
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertNotNull($model->cancelled_at);

        $model->forceFill(['cancelled_at' => null]);

        $model->save();

        $this->assertNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_update(): void
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertNotNull($model->cancelled_at);

        $model->forceFill(['cancelled_at' => null]);

        $model->update();

        $this->assertNull($model->fresh()->cancelled_at);
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_create_many(): void
    {
        $models = CancellableModel::factory()->cancelled()->count(2)->make();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => null])->save();
        });

        $this->assertCount(2, $models->fresh()->whereNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_save_many(): void
    {
        $models = CancellableModel::factory()->cancelled()->count(2)->create();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => null])->save();
        });

        $this->assertCount(2, $models->fresh()->whereNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_uncancelled_on_force_update_many(): void
    {
        $models = CancellableModel::factory()->cancelled()->count(2)->create();

        $this->assertCount(2, $models);

        $models->each(static function ($model) {
            $model->forceFill(['cancelled_at' => null])->update();
        });

        $this->assertCount(2, $models->fresh()->whereNull('cancelled_at'));
    }

    /** @test */
    public function a_model_can_be_queried_normally_when_not_cancelled(): void
    {
        CancellableModel::factory()->cancelled()->create();

        CancellableModel::factory()->create();

        $this->assertDatabaseCount('cancellable_models', 2);

        $this->assertCount(1, CancellableModel::all());
    }

    /** @test */
    public function a_model_cannot_be_queried_normally_when_cancelled(): void
    {
        CancellableModel::factory()->cancelled()->create();

        CancellableModel::factory()->create();

        $this->assertDatabaseCount('cancellable_models', 2);

        $this->assertCount(1, CancellableModel::all());
    }

    /** @test */
    public function all_models_can_be_found_with_the_withCancelled_scope(): void
    {
        CancellableModel::factory()->cancelled()->create();
        CancellableModel::factory()->create();

        $this->assertCount(2, CancellableModel::withCancelled()->get());
    }

    /** @test */
    public function only_cancelled_models_can_be_found_with_the_onlyCancelled_scope(): void
    {
        CancellableModel::factory()->cancelled()->create();
        CancellableModel::factory()->create();

        $this->assertCount(1, CancellableModel::onlyCancelled()->get());
    }

    /** @test */
    public function models_with_the_cancellable_trait_are_scoped(): void
    {
        CancellableModel::factory()->create();

        $this->assertCount(1, CancellableModel::all());
    }

    /** @test */
    public function models_without_the_cancellable_trait_are_not_scoped(): void
    {
        RegularModel::factory()->create();

        $this->assertCount(1, RegularModel::all());
    }

    /** @test */
    public function models_with_the_cancellable_trait_can_be_soft_deleted(): void
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertCount(1, CancellableModel::withCancelled()->get());

        $model->delete();

        $this->assertCount(0, CancellableModel::all());
    }

    /** @test */
    public function models_without_the_cancellable_trait_can_be_soft_deleted(): void
    {
        $model = RegularModel::factory()->create();

        $this->assertCount(1, RegularModel::all());

        $model->delete();

        $this->assertCount(0, RegularModel::all());
    }

    /** @test */
    public function a_model_with_the_cancellable_trait_is_cancelled(): void
    {
        $model = CancellableModel::factory()->cancelled()->create();

        $this->assertCount(1, CancellableModel::withCancelled()->get());

        $this->assertTrue($model->fresh()->isCancelled());
    }

    /** @test */
    public function a_model_without_the_cancellable_trait_is_not_cancelled(): void
    {
        $model = CancellableModel::factory()->create();

        $this->assertCount(1, CancellableModel::withCancelled()->get());

        $this->assertFalse($model->fresh()->isCancelled());
    }

    /** @test */
    public function a_model_can_be_queried_normally_when_cancelled_at_a_later_date(): void
    {
        $first = CancellableModel::factory()->create();
        $first->cancelled_at = Carbon::now()->addDay();
        $first->save();

        $this->assertDatabaseCount('cancellable_models', 1);

        $this->assertCount(1, CancellableModel::all());
        $this->assertCount(0, CancellableModel::onlyCancelled()->get());
        $this->assertCount(1, CancellableModel::withoutCancelled()->get());
        $this->assertCount(1, CancellableModel::withCancelled()->get());
    }

    /** @test */
    public function a_model_cannot_be_queried_normally_when_cancelled_at_a_previous_date(): void
    {
        $first = CancellableModel::factory()->create();
        $first->cancelled_at = Carbon::now()->subDay();
        $first->save();

        $this->assertDatabaseCount('cancellable_models', 1);

        $this->assertCount(0, CancellableModel::all());
        $this->assertCount(1, CancellableModel::onlyCancelled()->get());
        $this->assertCount(0, CancellableModel::withoutCancelled()->get());
        $this->assertCount(1, CancellableModel::withCancelled()->get());
    }

}
