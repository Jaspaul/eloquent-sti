<?php

namespace Tests;

use DB;
use Illuminate\Support\Str;
use Jaspaul\EloquentSTI\TypeScope;
use Tests\Helpers\StrictTypes\Car;
use Tests\Helpers\Inheritable\User;
use Tests\Helpers\StrictTypes\Plane;
use Tests\Helpers\StrictTypes\Vehicle;
use Tests\Helpers\Inheritable\Administrator;
use Jaspaul\EloquentSTI\Exceptions\UndefinedTypeException;

class InheritableTest extends TestCase
{
    /**
     * @test
     */
    public function inheritable_objects_are_automatically_resolved_into_their_respective_objects()
    {
        DB::table('users')->insert([
            ['type' => 'user', 'name' => 'Daffy Duck'],
            ['type' => 'administrator', 'name' => 'Donald Duck']
        ]);

        $daffy = User::where('name', 'Daffy Duck')->firstOrFail();
        $donald = User::where('name', 'Donald Duck')->firstOrFail();

        $this->assertInstanceOf(User::class, $daffy);
        $this->assertInstanceOf(Administrator::class, $donald);
    }

    /**
     * @test
     */
    public function inheritable_objects_are_automatically_resolved_to_the_querying_class_if_the_type_is_not_defined_in_the_types_array()
    {
        DB::table('users')->insert([
            ['type' => Str::random(40), 'name' => 'Daffy Duck']
        ]);

        $daffy = User::where('name', 'Daffy Duck')->firstOrFail();
        $this->assertInstanceOf(User::class, $daffy);

        $daffy = Administrator::where('name', 'Daffy Duck')->firstOrFail();
        $this->assertInstanceOf(Administrator::class, $daffy);
    }

    /**
     * @test
     */
    public function strictly_typed_inheritable_objects_are_only_accessible_through_their_respective_classes()
    {
        DB::table('vehicles')->insert([
            ['custom_type_column' => 'vehicle', 'name' => 'Bicycle'],
            ['custom_type_column' => 'car', 'name' => 'Ford F-150']
        ]);

        $vehicles = Vehicle::all();
        $cars = Car::all();

        $this->assertCount(1, $vehicles);
        $this->assertInstanceOf(Vehicle::class, $vehicles->first());

        $this->assertCount(1, $cars);
        $this->assertInstanceOf(Car::class, $cars->first());

        $this->assertEmpty(Vehicle::find($cars->first()->id));
        $this->assertEmpty(Car::find($vehicles->first()->id));
    }

    /**
     * @test
     */
    public function inheritable_objects_can_have_their_types_customized_and_changed_at_run_time()
    {
        $user = new User(['type' => 'user', 'name' => 'Daffy Duck']);
        $administrator = new User(['type' => 'administrator', 'name' => 'Donald Duck']);

        $user->save();
        $administrator->save();

        $daffy = User::where('name', 'Daffy Duck')->firstOrFail();
        $donald = User::where('name', 'Donald Duck')->firstOrFail();

        $this->assertInstanceOf(User::class, $daffy);
        $this->assertInstanceOf(Administrator::class, $donald);

        $daffy->type = 'administrator';
        $daffy->save();

        $this->assertInstanceOf(Administrator::class, $daffy->fresh());

        $donald->type = 'user';
        $donald->save();

        $this->assertInstanceOf(User::class, $daffy->fresh());
    }

    /**
     * @test
     */
    public function strictly_typed_inheritable_objects_will_automatically_set_their_types_before_saving_and_cannot_be_changed_or_customized()
    {
        $vehicle = new Vehicle(['name' => 'Bicycle']);
        $car = new Car(['name' => 'Ford F-150']);

        $vehicle->save();
        $car->save();

        $bicycle = Vehicle::findOrFail($vehicle->id);
        $ford = Car::findOrFail($car->id);

        $this->assertInstanceOf(Vehicle::class, $bicycle);
        $this->assertInstanceOf(Car::class, $ford);

        $bicycle->custom_type_column = 'car';
        $bicycle->save();

        $this->assertInstanceOf(Vehicle::class, $bicycle->fresh());

        $car->custom_type_column = 'vehicle';
        $car->save();

        $this->assertInstanceOf(Car::class, $car->fresh());
    }

    /**
     * @test
     */
    public function inheritable_objects_allow_the_customization_of_the_type_column()
    {
        DB::table('vehicles')->insert([
            ['custom_type_column' => 'car', 'name' => 'Ford F-150']
        ]);

        $cars = Car::all();

        $this->assertCount(1, $cars);
        $this->assertInstanceOf(Car::class, $cars->first());
        $this->assertSame('Ford F-150', $cars->first()->name);
        $this->assertSame('car', $cars->first()->getTypeValue());
        $this->assertSame('custom_type_column', $cars->first()->getTypeColumn());
    }

    /**
     * @test
     */
    public function strictly_typed_inheritable_objects_throw_an_exception_before_saving_if_the_class_is_not_defined_in_the_types_array()
    {
        $this->expectException(UndefinedTypeException::class);

        $plane = new Plane(['name' => '747']);
        $plane->save();

        $this->assertEmpty(DB::table('vehicles')->get());
    }
}
