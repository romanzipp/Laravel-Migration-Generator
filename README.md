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

## Usage

```shell
php artisan mg:generate {--connection=}
```

You will see various new migration files prefixed with the current time & date.

**Notice**: This package can't guess the order in which migrations will be created. If you've created some foreign keys in your database, be sure to re-order the migration files based on these relations.

## Supported Databases

- MySql (MariaDB)
- SQLite ([Info on Datatypes](https://www.sqlite.org/datatype3.html))

## Testing

```
./vendor/bin/phpunit
```

```
./vendor/bin/phpunit -c phpunit.mysql.xml
```
