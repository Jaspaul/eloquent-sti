<?php

namespace Tests;

use DB;
use Tests\Helpers\User;
use Tests\Helpers\Administrator;
use Tests\Helpers\Vehicle;
use Tests\Helpers\Car;
use Jaspaul\EloquentSTI\TypeScope;

class InheritableTest extends TestCase
{
    /**
     * @test
     */
    public function it_resolves_user_objects_when_the_type_is_user()
    {
        DB::table('users')->insert([
            'type' => 'user',
            'name' => 'Derp'
        ]);

        $users = User::all();

        $this->assertCount(1, $users);
        $this->assertInstanceOf(User::class, $users->first());
    }

    /**
     * @test
     */
    public function it_resolves_administrator_objects_when_the_type_is_administrator()
    {
        DB::table('users')->insert([
            'type' => 'administrator',
            'name' => 'Derp'
        ]);
        $users = User::all();

        $this->assertCount(1, $users);
        $this->assertInstanceOf(Administrator::class, $users->first());
    }

    /**
     * @test
     */
    public function it_resolves_user_objects_when_the_type_is_unknown()
    {
        DB::table('users')->insert([
            'type' => str_random(40),
            'name' => 'Derp'
        ]);

        $users = User::all();

        $this->assertCount(1, $users);
        $this->assertInstanceOf(User::class, $users->first());
    }

    /**
     * @test
     */
    public function it_resolves_administrator_objects_when_a_user_is_saved_with_an_administrator_type()
    {
        $user = new User([
            'type' => 'administrator',
            'name' => 'HERP'
        ]);

        $user->save();

        $this->assertInstanceOf(Administrator::class, User::findOrFail($user->id));
    }

    /**
     * @test
     */
    public function it_saves_an_administrator_object_to_the_users_table_by_default()
    {
        $administrator = new Administrator([
            'type' => 'administrator',
            'name' => 'DERP'
        ]);

        $administrator->save();

        $this->assertInstanceOf(Administrator::class, User::findOrFail($administrator->id));
    }

    /**
     * @test
     */
    public function it_saves_a_user_object_to_the_users_table_by_default()
    {
        $user = new User([
            'type' => 'user',
            'name' => 'DERP'
        ]);

        $user->save();

        $this->assertInstanceOf(User::class, User::findOrFail($user->id));
    }

    /**
     * @test
     */
    public function it_allows_the_customization_of_the_types_column()
    {
        $car = new Car([
            'custom_type_column' => 'car'
        ]);

        $car->save();

        $this->assertInstanceOf(Car::class, Car::findOrFail($car->id));
    }

    /**
     * @test
     */
    public function it_automatically_scopes_the_query_to_only_return_objects_matching_the_class_type()
    {
        $car = new Car([]);
        $car->save();

        $vehicle = new Vehicle([]);
        $vehicle->save();

        $this->assertCount(1, Car::all());
        $this->assertInstanceOf(Car::class, Car::all()->first());

        $this->assertCount(1, Vehicle::all());
        $this->assertInstanceOf(Vehicle::class, Vehicle::all()->first());

        $this->assertCount(2, Vehicle::withoutGlobalScope(TypeScope::class)->get());
    }
}
