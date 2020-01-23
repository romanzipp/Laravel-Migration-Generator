# Laravel Migration Generator

Generate Laravel migration files from existing databases.

## Installation

```
composer require romanzipp/laravel-migration-generator
```

**If you use Laravel 5.5+ you are already done, otherwise continue.**

Add Service Provider to your `app.php` configuration file:

```php
romanzipp\MigrationGenerator\Providers\MigrationGeneratorProvider::class,
```

## Configuration

Copy configuration to config folder:

```
$ php artisan vendor:publish --provider="romanzipp\MigrationGenerator\Providers\MigrationGeneratorProvider"
```

## Testing

```
./vendor/bin/phpunit
```
