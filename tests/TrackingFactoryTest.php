<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tests\Tracking\Support\StubTracker;
use BoxedCode\Tracking\Contracts\Tracker;
use BoxedCode\Tracking\TrackerFactory;
use stdClass;

class TrackingFactoryTest extends AbstractTestCase
{
    public function testConstructor()
    {
        $f = $this->getFactory();

        $this->assertInstanceOf(TrackerFactory::class, $f);
    }

    public function testDestroyIdentifier()
    {
        $m = $this->createStubResource();

        $f = $this->getFactory();

        $f->destroy($m->id);

        $this->assertNull($m::find($m->id));
    }

    public function testDestroyModel()
    {
        $m = $this->createStubResource();

        $f = $this->getFactory();

        $f->destroy($m);

        $this->assertNull($m::find($m->id));
    }

    public function testResourceIdentifier()
    {
        $m = $this->createStubResource();

        $f = $this->getFactory();

        $t = $f->resource($m->id);

        $this->assertInstanceOf(Tracker::class, $t);
    }

    public function testResourceModel()
    {
        $m = $this->createStubResource();

        $f = $this->getFactory();

        $t = $f->resource($m);

        $this->assertInstanceOf(Tracker::class, $t);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResourceModelInvalid()
    {
        $f = $this->getFactory();

        $t = $f->resource(new stdClass);
    }

    public function testCall()
    {
        $f = $this->getFactory();

        $this->assertInstanceOf(Tracker::class, $f->stub());
    }

    protected function getFactory()
    {
        return new TrackerFactory($this->app, [StubTracker::class]);
    }
}
