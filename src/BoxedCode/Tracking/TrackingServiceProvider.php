<?php

namespace BoxedCode\Tracking;

use BoxedCode\Tracking\Trackers\PixelTracker;
use BoxedCode\Tracking\Trackers\RedirectTracker;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TrackingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerTrackers();

        $this->app->singleton(TrackerFactory::class, function ($app) {
            $trackers = [
                PixelTracker::class,
                RedirectTracker::class,
            ];

            return new TrackerFactory($app, $trackers);
        });

        $this->app->alias(TrackerFactory::class, 'tracking');
    }

    public function registerTrackers()
    {
        $this->app->bind(PixelTracker::class, function ($app) {
            return new PixelTracker($app, $app['events'], $app['config']);
        });

        $this->app->bind(RedirectTracker::class, function ($app) {
            return new RedirectTracker($app, $app['events'], $app['config']);
        });
    }

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
