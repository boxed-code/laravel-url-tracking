<?php

namespace BoxedCode\Tests\Tracking\Support;

use BoxedCode\Tracking\TrackingFacade;
use BoxedCode\Tracking\TrackingServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        parent::getEnvironmentSetUp($app);
    }

    public function setUp()
    {
        parent::setUp();

        $this->artisan('vendor:publish', [
            '--provider' => TrackingServiceProvider::class,
            '--force' => true,
        ]);

        $this->migrate();
    }

    protected function migrate()
    {
        $artisan = app()->make('Illuminate\Contracts\Console\Kernel');

        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--realpath' => app()->databasePath().'/migrations',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [TrackingServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Tracking' => TrackingFacade::class,
        ];
    }
}
