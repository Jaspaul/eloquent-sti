<?php

namespace Tests\Helpers;

use Jaspaul\EloquentSTI\Inheritable;
use Illuminate\Database\Eloquent\Model;
use Jaspaul\EloquentSTI\StrictTypes;

class Vehicle extends Model
{
    use StrictTypes, Inheritable;

    protected $typeColumn = 'custom_type_column';

    protected $guarded = [];

    protected $types = [
        'vehicle' => Vehicle::class,
        'car' => Car::class
    ];
}
