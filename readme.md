# Eloquent STI (Single Table Inheritance)

## Install

Via Composer

``` bash
$ composer require jaspaul/eloquent-sti
```

## Requirements

The following versions of PHP are supported by this version.

* PHP 7.0
* PHP 7.1
* PHP 7.2

## Usage

```php
<?php

use Tests\Helpers\User;
use Tests\Helpers\Administrator;
use Jaspaul\EloquentSTI\Inheritable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Inheritable;

    /**
     * Provides a map of types to resolve for this object. The format is:
     *     'user' => User::class,
     *     'administrator' => Administrator::class
     *
     * @var array
     */
    protected $types = [
        'user' => User::class,
        'administrator' => Administrator::class
    ];
}
```

```php
<?php

class Administrator extends User
{
}
```

Now when you select users through the User model, they'll be returned with the associated type. For instance if you have a record in the users table with the type administrator, an Administrator object will be returned when you run `User::where('type', 'administrator')->first()`.
