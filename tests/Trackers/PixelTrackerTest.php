<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\Trackers\PixelTracker;
use League\Url\Url;

class PixelTrackerTest extends AbstractTestCase
{
    public function testHandle()
    {
        $pixel = base64_decode(
            'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='
        );

        $t = $this->createPixelTracker();

        $response = $this->call('GET', $t);

        $this->assertSame('image/gif', $response->headers->get('Content-Type'));

        $this->assertSame($pixel, $response->original);

        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testGetTrackedUrl()
    {
        $t = $this->createPixelTracker();

        $url = route($t->getRouteName(), $t->getModel()->getKey());

        $this->assertSame($url, (string) $t->getTrackedUrl());

        $this->assertStringEndsWith('.gif', (string) $t);
    }

    public function testGetTrackedUrlCustomParameters()
    {
        $t = $this->createPixelTracker();

        $url = $t->getTrackedUrl(['foo' => 'bar']);

        $this->assertStringEndsWith('foo=bar', (string) $url);
    }

    protected function createPixelTracker()
    {
        $t = new PixelTracker($this->app, $this->app['events'], $this->app['config']);

        $t->setModel($this->createTrackableResource());

        return $t;
    }
}
