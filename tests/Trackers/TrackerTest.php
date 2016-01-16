<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tests\Tracking\Support\StubTracker;
use BoxedCode\Tracking\Contracts\Tracker;
use League\Url\Url;

class TrackerTest extends AbstractTestCase
{
    public function testConstructor()
    {
        $t = new StubTracker($this->app, $this->app['events'], $this->app['config']);

        $this->assertInstanceOf(Tracker::class, $t);
    }

    public function testGetHandle()
    {
        $t = $this->createStubTracker();

        $this->assertSame('stub', $t->getHandle());
    }

    public function testGetRouteName()
    {
        $t = $this->createStubTracker();

        $this->assertSame('tracking.stub', $t->getRouteName());
    }

    public function testGetRouteKey()
    {
        $t = $this->createStubTracker();

        $this->assertSame('s', $t->getRouteKey());
    }

    public function testSetGetModel()
    {
        $m = $this->createStubResource();

        $t = $this->app[StubTracker::class]->setModel($m);

        $this->assertSame($m, $t->getModel());
    }

    public function testSetGetRoutingParameter()
    {
        $t = $this->createStubTracker();

        $t->setRoutingParameter('z');

        $this->assertSame('z', $t->getRoutingParameter());
    }

    public function testRegisterRoute()
    {
        $t = $this->createStubTracker();

        $response = $this->call('GET', $t);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRegisterRouteNotFound()
    {
        $t = $this->createStubTracker();

        $url = $t->getTrackedUrl();

        $response = $this->call('GET', $url.'invalid');
    }

    public function testRegisterRouteEvent()
    {
        $t = $this->createStubTracker();

        // Smells vv
        $called = false;

        $this->app['events']->listen('tracking.tracked', function () use (&$called) {
            $called = true;
        });

        $this->call('GET', $t);

        $this->assertTrue($called);
    }

    public function testGetModelAttributes()
    {
        $t = $this->createStubTracker();

        $attr = $t->getModelAttributes(['foo' => 'bar']);

        $this->assertStringMatchesFormat('%c%c%c%c%c%c', $attr['id']);

        $this->assertSame(StubTracker::class, $attr['type']);

        $this->assertSame('bar', $attr['foo']);
    }

    public function testGetTrackedUrl()
    {
        $t = $this->createStubTracker();

        $url = route($t->getRouteName(), $t->getModel()->getKey());

        $this->assertSame($url, (string) $t->getTrackedUrl());
    }

    public function testGetTrackedUrlCustomParameters()
    {
        $t = $this->createStubTracker();

        $url = $t->getTrackedUrl(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertStringEndsWith('foo=bar&baz=qux', (string) $url);
    }

    public function testToString()
    {
        $t = $this->createStubTracker();

        $this->assertSame((string) $t->getTrackedUrl(), $t->__toString());
    }
}
