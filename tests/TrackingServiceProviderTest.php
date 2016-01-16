<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\TrackerFactory;
use BoxedCode\Tracking\Trackers\PixelTracker;
use BoxedCode\Tracking\Trackers\RedirectTracker;
use BoxedCode\Tracking\TrackingServiceProvider;

class TrackingServiceProviderTest extends AbstractTestCase
{
    public function testFactoryInjectable()
    {
        $this->assertInstanceOf(
            TrackerFactory::class, $this->app->make(TrackerFactory::class)
        );
    }

    public function testPixelTrackerInjectable()
    {
        $this->assertInstanceOf(
            PixelTracker::class,
            $this->app->make(PixelTracker::class)
        );
    }

    public function testPixelTrackerRoute()
    {
        $t = $this->app->make(PixelTracker::class);

        $t->setModel($this->createTrackableResource());

        $response = $this->call('GET', $t->getTrackedUrl());

        $this->assertEquals(200, $response->status());

        $this->assertEquals('image/gif', $response->headers->get('Content-Type'));
    }

    public function testRedirectTrackerInjectable()
    {
        $this->assertInstanceOf(
            RedirectTracker::class,
            $this->app->make(RedirectTracker::class)
        );
    }

    public function testRedirectTrackerRoute()
    {
        $t = $this->app->make(RedirectTracker::class);

        $t->setModel($this->createTrackableResource(['resource' => 'http://www.google.co.uk']));

        $response = $this->call('GET', $t->getTrackedUrl());

        $this->assertEquals(302, $response->status());
    }

    public function testPublishedAssets()
    {
        $publishes = TrackingServiceProvider::pathsToPublish();

        $this->assertCount(2, $publishes);
    }
}
