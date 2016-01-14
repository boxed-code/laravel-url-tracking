<?php

namespace BoxedCode\Tests\Tracking\Support;

use BoxedCode\Tracking\TrackableResourceModel;
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

        // Bind the stub tracker
        $app->bind(StubTracker::class, function ($app) {
           return new StubTracker($app, $app['events'], $app['config']);
        });

        $app[StubTracker::class]->registerRoute($app['router']);

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

    protected function createTrackableResource($attrs = [])
    {
        $attrs = array_merge(
            [
                'id' => 12345,
                'type' => 'test',
            ],
            $attrs
        );

        return TrackableResourceModel::create($attrs);
    }

    protected function createStubResource($attrs = [])
    {
        $attrs = array_merge(
            [
                'id' => str_random(7),
                'type' => StubTracker::class,
            ],
            $attrs
        );

        return TrackableResourceModel::create($attrs);
    }

    protected function createStubTracker($attrs = [])
    {
        return $this->app[StubTracker::class]->setModel($this->createStubResource($attrs));
    }
}
