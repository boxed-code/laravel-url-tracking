<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\Contracts\Tracker;
use BoxedCode\Tracking\TrackableResourceModel;
use BoxedCode\Tracking\TrackerFactory;
use BoxedCode\Tracking\Trackers\PixelTracker;

class TrackingFactoryTest extends AbstractTestCase
{
    public function testConstructor()
    {
        $f = $this->getFactory();

        $this->assertInstanceOf(TrackerFactory::class, $f);
    }

    public function testDestroyIdentifier()
    {
        $m = $this->createPixelResource();

        $f = $this->getFactory();

        $f->destroy($m->id);

        $this->assertNull($m::find($m->id));
    }

    public function testDestroyModel()
    {
        $m = $this->createPixelResource();

        $f = $this->getFactory();

        $f->destroy($m);

        $this->assertNull($m::find($m->id));
    }

    public function testResourceIdentifier()
    {
        $m = $this->createPixelResource();

        $f = $this->getFactory();

        $t = $f->resource($m->id);

        $this->assertInstanceOf(Tracker::class, $t);
    }

    public function testResourceModel()
    {
        $m = $this->createPixelResource();

        $f = $this->getFactory();

        $t = $f->resource($m);

        $this->assertInstanceOf(Tracker::class, $t);
    }

    public function testCall()
    {
        $f = $this->getFactory();

        $this->assertInstanceOf(Tracker::class, $f->pixel());
    }

    protected function getFactory()
    {
        return new TrackerFactory($this->app, [PixelTracker::class]);
    }

    protected function createPixelResource($attrs = [])
    {
        $attrs = array_merge(
            [
                'id' => str_random(7),
                'type' => PixelTracker::class,
            ],
            $attrs
        );

        return TrackableResourceModel::create($attrs);
    }
}
