<?php

namespace BoxedCode\Tracking;

use Illuminate\Http\Request;

class TrackedEvent
{
    public $model;

    public $request;

    public function __construct(TrackableResourceModel $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }
}
