<?php

namespace BoxedCode\Tracking\Trackers;

use BoxedCode\Tracking\Contracts\Tracker as TrackerContract;
use BoxedCode\Tracking\TrackableResourceModel;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RedirectTracker extends Tracker implements TrackerContract
{
    protected $handle = 'url';

    protected $route_name = 'tracking.redirect';

    protected $route_key = 'r';

    public function handle(Request $request, TrackableResourceModel $model)
    {
        return redirect()->away($model->resource, $model->meta['status_code']);
    }

    public function validateArguments(array $args)
    {
        if (! isset($args[0]) || ! filter_var($args[0], FILTER_VALIDATE_URL)) {
            $url = isset($args[0]) ? (string) $args[0] : 'none';

            throw new InvalidArgumentException("Invalid url provided. [$url]");
        }

        if (isset($args[1])) {
            if (! is_int($args[1])) {
                throw new InvalidArgumentException("Invalid status code. [$args[1]");
            }
        }
    }

    public function transformArguments(array $args)
    {
        return [
            'resource' => $args[0],
            'meta' => [
                'status_code' => isset($args[1]) ? $args[1] : 302,
            ],
        ];
    }
}
