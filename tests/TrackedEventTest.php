<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\TrackableResourceModel;
use BoxedCode\Tracking\TrackedEvent;
use Illuminate\Http\Request;

class TrackedEventTest extends AbstractTestCase
{
    public function testContructor()
    {
        $e = new TrackedEvent(new TrackableResourceModel(), $this->app['request']);

        $this->assertInstanceOf(Request::class, $e->request);
        $this->assertInstanceOf(TrackableResourceModel::class, $e->model);
    }
}
