<?php

namespace LaravelCancellable\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelCancellable\Tests\TestClasses\CancellableModel;

class CancellableModelFactory extends Factory
{
    protected $model = CancellableModel::class;

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'cancelled_at' => now(),
            ];
        });
    }

    public function definition()
    {
        return [];
    }
}
