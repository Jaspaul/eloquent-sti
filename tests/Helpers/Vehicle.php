<?php

namespace Tests\Helpers;

use Jaspaul\EloquentSTI\Inheritable;
use Illuminate\Database\Eloquent\Model;
use Jaspaul\EloquentSTI\HandlesTypes;

class Vehicle extends Model
{
    use HandlesTypes, Inheritable;

    protected $typeColumn = 'custom_type_column';

    protected $guarded = [];

    protected $types = [
        'vehicle' => Vehicle::class,
        'car' => Car::class
    ];
}
