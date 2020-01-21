<?php

namespace Tests;

use Mockery;
use Orchestra\Testbench\TestCase as Base;
use Orchestra\Database\ConsoleServiceProvider;

abstract class TestCase extends Base
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Helpers/Migrations');
        $this->artisan('migrate', ['--database' => 'sqlite']);
    }

    protected function getPackageProviders($app)
    {
        return [ConsoleServiceProvider::class];
    }
}
