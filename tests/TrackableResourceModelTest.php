<?php

namespace BoxedCode\Tests\Tracking;

use BoxedCode\Tests\Tracking\Support\AbstractTestCase;
use BoxedCode\Tracking\TrackableResourceModel;

class TrackableResourceModelTest extends AbstractTestCase
{
    public function testSetGetMetaAttribute()
    {
        $m = $this->getModel();

        $meta = ['foo' => 'bar'];

        $m->meta = $meta;

        $this->assertSame($meta, $m->meta);
    }

    public function testSetGetMetaAttributeNull()
    {
        $m = $this->getModel();

        $meta = ['foo' => 'bar'];

        $m->meta = $meta;

        $m->meta = null;

        $this->assertNull($m->meta);
    }

    protected function getModel()
    {
        return new TrackableResourceModel();
    }
}
