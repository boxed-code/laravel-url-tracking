<?php

namespace BoxedCode\Tracking;

/*
 * $tracker = new RedirectTracker('http://www.google.co.uk');
 * $url = $tracker->getTrackingUrl();
 *
 * $tracker = new PixelTracker();
 * $url = $tracker->getTrackingUrl();
 *
 * $link = Tracking::url('http://www.google.co.uk')->getTrackedUrl(['foo' => 'bar']);
 * $link = Tracking::pixel()->getTrackingUrl(['foo' => 'bar']);
 *
 * $tracking = app('uri.tracking');
 * $tracker = $tracking->url('http://www.google.co.uk');
 * $link = $tracking->url('http://www.google.co.uk')->getTrackedUrl(['foo' => 'bar']);
 * $tracker = $tracking->pixel();
 * $tracker = $tracking->trackerFromModel($model);
 * $tracking->destroy('ACKSROVMSKE34H45FG');
 *
 */

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class TrackerFactory
{
    protected $container;

    protected $trackers;

    public function __construct(Container $container, array $trackers)
    {
        $this->container = $container;

        foreach ($trackers as $abstract) {
            $handle = $container[$abstract]->getHandle();

            $this->trackers[$handle] = $abstract;
        }
    }

    public function destroy($resource)
    {
        if (is_string($resource)) {
            $resource = TrackableResourceModel::find($resource);
        }

        if (! $resource instanceof TrackableResourceModel) {
            throw new InvalidArgumentException(
                "Invalid resource, must be string identifier or TrackableResourceModel. [$name]"
            );
        }

        $resource->delete();
    }

    public function resource($resource)
    {
    }

    protected function getUniqueId()
    {
        while (! isset($token) || TrackableResourceModel::find($token)) {
            $token = str_random(6);
        }

        return $token;
    }

    public function __call($name, $arguments = [])
    {
        if (array_key_exists($name, $this->trackers)) {
            $tracker = $this->container->make($this->trackers[$name]);

            $tracker->validateArguments($arguments);

            $attr = [
                'id' => $this->getUniqueId(),
                'type' => get_class($tracker),
            ];

            $attr = array_merge(
                $tracker->transformArguments($arguments), $attr
            );

            $tracker->setModel(TrackableResourceModel::create($attr));

            return $tracker;
        }
    }
}
