<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\Trackers\RedirectTracker;

class RedirectTrackerTest extends AbstractTestCase
{
    public function testHandle()
    {
        $t = $this->createRedirectTracker();

        $response = $this->call('GET', $t);

        $this->assertSame(302, $response->status());
    }

    public function testHandleCustomStatusCode()
    {
        $m = $this->createTrackableResource([
            'resource' => 'http://www.google.co.uk',
            'meta' => [
                'status_code' => 301,
            ],
        ]);

        $t = $this->createRedirectTracker();

        $t->setModel($m);

        $response = $this->call('GET', $t);

        $this->assertSame(301, $response->status());
    }

    public function testGetModelAttributes()
    {
        $t = $this->createRedirectTracker();

        $attr = $t->getModelAttributes(['http://google.com']);

        $this->assertSame('http://google.com', $attr['resource']);

        $this->assertNull($attr['meta']['status_code']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetModelAttributesInvalidUrl()
    {
        $t = $this->createRedirectTracker();

        $attr = $t->getModelAttributes(['sdle.com']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetModelAttributesInvalidStatusCode()
    {
        $t = $this->createRedirectTracker();

        $attr = $t->getModelAttributes(['http://google.com', 'abc']);
    }

    protected function createRedirectTracker()
    {
        $t = new RedirectTracker($this->app, $this->app['events'], $this->app['config']);

        $t->setModel($this->createTrackableResource(['resource' => 'http://www.google.co.uk']));

        return $t;
    }
}
