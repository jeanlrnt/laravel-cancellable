A simple package for making Laravel Eloquent models 'cancellable'. This package allows for the easy cancelling of models by creating various macros to be used within method chaining.

## Installation

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

If you discover any security related issues, please email joel@joelbutcher.co.uk instead of using the issue tracker.

## Credits

- [Joel Butcher](https://github.com/joelbutcher)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
