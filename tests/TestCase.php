<?php

namespace Tests;

use Mockery;
use Orchestra\Testbench\TestCase as Base;

abstract class TestCase extends Base
{
    /**
     * @before
     */
    protected function setUpMockery()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(false);
    }

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Helpers/Migrations');
        $this->artisan('migrate', ['--database' => 'sqlite']);
    }
}
