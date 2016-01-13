<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\TrackerFactory;

class TrackingFacadeTest extends AbstractTestCase
{
    public function testFacade()
    {
        $factory = \Tracking::getFacadeRoot();

        $this->assertInstanceOf(TrackerFactory::class, $factory);
    }
}
