<?php

namespace Tests;

use Tests\Helpers\Vehicle;
use Tests\Helpers\Car;
use Tests\Helpers\Plane;
use Jaspaul\EloquentSTI\Exceptions\UndefinedTypeException;

class StrictTypesTest extends TestCase
{
    /**
     * @test
     */
    public function it_automatically_sets_the_type_column_before_saving_the_model()
    {
        $car = new Car(['name' => 'Benz']);
        $car->save();

        $result = Car::findOrFail($car->id);

        $this->assertInstanceOf(Car::class, $result);
        $this->assertSame('car', $result->custom_type_column);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_before_saving_if_the_class_is_not_in_the_types_array()
    {
        $this->expectException(UndefinedTypeException::class);
        $plane = new Plane(['name' => '747']);
        $plane->save();
        $this->assertEmpty(Vehicle::all());
    }
}
