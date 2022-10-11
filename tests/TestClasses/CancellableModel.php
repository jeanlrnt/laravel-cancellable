<?php

namespace LaravelCancellable\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelCancellable\Cancellable;

class CancellableModel extends Model
{
    use Cancellable;
    use HasFactory;
}
