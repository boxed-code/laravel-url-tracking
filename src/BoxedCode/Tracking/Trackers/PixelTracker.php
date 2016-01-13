<?php

namespace BoxedCode\Tracking\Trackers;

use BoxedCode\Tracking\Contracts\Tracker as TrackerContract;
use BoxedCode\Tracking\TrackableResourceModel;
use Illuminate\Http\Request;

class PixelTracker extends Tracker implements TrackerContract
{
    protected $handle = 'pixel';

    protected $route_name = 'tracking.pixel';

    protected $route_key = 'p';

    public function handle(Request $request, TrackableResourceModel $model)
    {
        $pixel = base64_decode(
            'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='
        );

        return response($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function getTrackedUrl($parameters = [])
    {
        $url = parent::getTrackedUrl($parameters);

        $path = $url->getPath();

        $length = count($path) - 1;

        $path->offsetSet($length, $path[$length].'.gif');

        return $url;
    }
}
