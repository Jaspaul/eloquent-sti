<?php

namespace Tests\Helpers\Inheritable;

use Jaspaul\EloquentSTI\Inheritable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Inheritable;

    protected $guarded = [];

    protected $types = [
        'user' => User::class,
        'administrator' => Administrator::class
    ];
}
