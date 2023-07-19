<p align="center">
<a href="https://github.com/jeanlrnt/laravel-cancellable/actions"><img src="https://github.com/jeanlrnt/laravel-cancellable/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/jeanlrnt/laravel-cancellable"><img src="https://img.shields.io/packagist/dt/jeanlrnt/laravel-cancellable" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jeanlrnt/laravel-cancellable"><img src="https://img.shields.io/packagist/v/jeanlrnt/laravel-cancellable" alt="Last stable version"></a>
<a href="https://packagist.org/packages/jeanlrnt/laravel-cancellable"><img src="https://img.shields.io/packagist/l/jeanlrnt/laravel-cancellable" alt="License"></a>
</p>

A simple package for making Laravel Eloquent models 'cancellable'. This package allows for the easy cancelling of models by creating various macros to be used within method chaining.

## Installation & usage

This package requires PHP 7.3 or higher and Laravel 6.0 or higher.

You can install the package via composer:

```bash
composer require jeanlrnt/laravel-cancellable
```

## Usage

#### Migrations

The `Cancellable` trait works similarly to Laravel's `SoftDeletes` trait. This package also ships with a helpful macro for Laravel's `\Illuminate\Database\Schema\Blueprint`. To get started, simply add the `cancelledAt` macro to your migration, like so:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('title');
    $table->timestamps();
    $table->cancelledAt(); // Macro
});
```

#### Eloquent
You can now, safely, include the `Cancellable` trait in your Eloquent model:

``` php
namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use \LaravelCancellable\Cancellable;

class Post extends Model {

    use Cancellable;
    ...
}
```

#### Extensions

The extensions shipped with this trait include; `cancel`, `unCancel`, `withCancelled`, `withoutCancelled`, `onlyCancelled` and can be used accordingly:

```php
$user = User::first();
$user->cancel();
$user->unCancel();

$usersWithCanceled = User::query()->withCanceled();
$onlyCanceledUsers = User::query()->onlyCanceled();
```

By default, the global scope of this trait uses the `withoutCanceled` extension when the trait is added to a model.

### Testing

```composer test```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email contact@jeanlaurent.fr instead of using the issue tracker.

## Credits

- [Joel Butcher](https://github.com/joelbutcher)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
