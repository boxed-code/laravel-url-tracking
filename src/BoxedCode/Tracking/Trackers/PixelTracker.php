<?php

namespace BoxedCode\Tracking\Trackers;

use BoxedCode\Tracking\Contracts\Tracker as TrackerContract;
use BoxedCode\Tracking\TrackableResourceModel;
use Illuminate\Http\Request;

class PixelTracker extends Tracker implements TrackerContract
{
    /**
     * The trackers type handle.
     *
     * @var string
     */
    protected $handle = 'pixel';

    /**
     * The trackers route name.
     *
     * @var string
     */
    protected $route_name = 'tracking.pixel';

    /**
     * The trackers route parameter.
     *
     * @var string
     */
    protected $route_parameter = 'p';

    /**
     * Get the routing path.
     *
     * @return string
     */
    protected function getRoutingPath()
    {
        $path = parent::getRoutingPath();

        return $path.'.gif';
    }

    /**
     * Handle the tracking request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return mixed
     */
    public function handle(Request $request, TrackableResourceModel $model)
    {
        $pixel = base64_decode(
            'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='
        );

        return response($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'must-revalidate, no-cache, no-store, private');
    }
}
