<?php

namespace BoxedCode\Tracking\Trackers;

use BoxedCode\Tracking\Contracts\Tracker as TrackerContract;
use BoxedCode\Tracking\TrackableResourceModel;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RedirectTracker extends Tracker implements TrackerContract
{
    /**
     * The trackers type handle.
     *
     * @var string
     */
    protected $handle = 'url';

    /**
     * The trackers route name.
     *
     * @var string
     */
    protected $route_name = 'tracking.redirect';

    /**
     * The trackers route parameter.
     *
     * @var string
     */
    protected $route_parameter = 'r';

    /**
     * Handle the tracking request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return mixed
     */
    public function handle(Request $request, TrackableResourceModel $model)
    {
        $default_status = $this->config->get('tracking.redirect', 302);

        $status_code = $model->meta['status_code'] ?: $default_status;

        return redirect()->away($model->resource, $status_code);
    }

    /**
     * Generate a model attribute array from an argument array.
     *
     * @param array $args
     * @return array
     */
    public function getModelAttributes(array $args)
    {
        if (! isset($args[0]) || ! filter_var($args[0], FILTER_VALIDATE_URL)) {
            $url = isset($args[0]) ? (string) $args[0] : 'none';

            throw new InvalidArgumentException("Invalid url provided. [$url]");
        }

        if (isset($args[1]) && ! is_int($args[1])) {
            throw new InvalidArgumentException("Invalid status code. [$args[1]]");
        }

        return parent::getModelAttributes([
            'resource' => $args[0],
            'meta' => [
                'status_code' => isset($args[1]) ? $args[1] : null,
            ],
        ]);
    }
}
