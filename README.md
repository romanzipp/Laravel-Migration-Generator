# Laravel Migration Generator

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/laravel-migration-generator.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-migration-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/laravel-migration-generator.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-migration-generator)
[![License](https://img.shields.io/packagist/l/romanzipp/laravel-migration-generator.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-migration-generator)
[![Travis Build Status](https://img.shields.io/travis/romanzipp/Laravel-Migration-Generator/master.svg?style=flat-square)](https://travis-ci.org/romanzipp/Laravel-Migration-Generator)

**WORK IN PROGRESS**

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

- MySQL
- MariaDB
- SQLite ([Info on Datatypes](https://www.sqlite.org/datatype3.html))

## Features

- [x] Tables
- [x] Columns
- [x] Column modifiers (nullable, default, ...)
- [ ] Indexes
- [ ] Foreign keys

## Testing

There are [Travis](https://travis-ci.org/romanzipp/Laravel-Migration-Generator) tests with the following matrix.

- **PHP Versions**
  - 7.2
  - 7.2
  - 7.3
- Databases
  - SQLite
  - MySQL
  - MariaDB
- Composer packages
  - latest
  - lowest

### SQLite

```
./vendor/bin/phpunit
```

### MySQL / MariaDB

*Requires a running MySQL / MariaDB server*

```
./vendor/bin/phpunit -c phpunit.mysql.xml
```

#### Provide database environment variables 

```
DB_HOST=127.0.0.1 DB_USERNAME=user DB_PASSWORD=secret ./vendor/bin/phpunit -c phpunit.mysql.xml
```

## Known issues

- *MariaDB*: The `JSON` MySQL data type is not supported since Doctrine/DBAL interprets this as `Doctrine\DBAL\Types\TextType`
- *MySQL*: Doctrine/DBAL can't differentiate between TEXT, LONGTEXT, MEDIUMTEXT
