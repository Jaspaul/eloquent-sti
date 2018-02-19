<?php

namespace Tests;

use DB;
use Tests\Helpers\User;
use Tests\Helpers\Administrator;

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
}
