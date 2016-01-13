<?php

namespace BoxedCode\Tracking;

use BoxedCode\Tracking\Trackers\PixelTracker;
use BoxedCode\Tracking\Trackers\RedirectTracker;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TrackingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTrackers();

        $this->registerFactory();
    }

    /**
     * Register the factory.
     */
    protected function registerFactory()
    {
        $this->app->singleton(TrackerFactory::class, function ($app) {
            $trackers = [
                PixelTracker::class,
                RedirectTracker::class,
            ];

            return new TrackerFactory($app, $trackers);
        });

        $this->app->alias(TrackerFactory::class, 'tracking');
    }

    /**
     * Register the trackers.
     */
    protected function registerTrackers()
    {
        $this->app->bind(PixelTracker::class, function ($app) {
            return new PixelTracker($app, $app['events'], $app['config']);
        });

        $this->app->bind(RedirectTracker::class, function ($app) {
            return new RedirectTracker($app, $app['events'], $app['config']);
        });
    }

    /**
     * Register the trackers routes and published package assets.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        $this->app[PixelTracker::class]->registerRoute($router);

        $this->app[RedirectTracker::class]->registerRoute($router);

        $this->publishes([
            realpath(__DIR__.'/../../../migrations/') => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            realpath(__DIR__.'/../../../config/') => config_path(),
        ], 'config');
    }
}
