<?php

namespace BoxedCode\Tests\Tracking\Support;

use BoxedCode\Tracking\Contracts\Tracker as TrackerContract;
use BoxedCode\Tracking\TrackableResourceModel;
use BoxedCode\Tracking\Trackers\Tracker;
use Illuminate\Http\Request;

class StubTracker extends Tracker implements TrackerContract
{
    protected $handle = 'stub';

    protected $route_name = 'tracking.stub';

    protected $route_parameter = 's';

    public function handle(Request $request, TrackableResourceModel $model)
    {
        return response()->make('Ok', 200);
    }
}
