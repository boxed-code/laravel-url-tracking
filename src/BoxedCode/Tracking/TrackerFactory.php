<?php

namespace BoxedCode\Tracking;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class TrackerFactory
{
    /**
     * Container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Tracker type array.
     *
     * @var array
     */
    protected $trackers;

    /**
     * TrackerFactory constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param array $trackers
     */
    public function __construct(Container $container, array $trackers)
    {
        $this->container = $container;

        foreach ($trackers as $abstract) {
            $handle = $container[$abstract]->getHandle();

            $this->trackers[$handle] = $abstract;
        }
    }

    /**
     * Source a model from an argument.
     *
     * @param $arg
     * @return mixed
     */
    protected function sourceModel($arg)
    {
        if (is_string($arg)) {
            $arg = TrackableResourceModel::findOrFail($arg);
        }

        if (! $arg instanceof TrackableResourceModel) {
            $name = ('object' === gettype($arg)) ? get_class($arg) : 'object';

            throw new InvalidArgumentException(
                "Invalid resource, must be string identifier or TrackableResourceModel. [$name]"
            );
        }

        return $arg;
    }

    /**
     * Destroy a tracker by id or model.
     *
     * @param string|\BoxedCode\Tracking\TrackableResourceModel $mixed
     */
    public function destroy($mixed)
    {
        $model = $this->sourceModel($mixed);

        $model->delete();
    }

    /**
     * Get a tracker by id or model.
     *
     * @param $mixed
     * @return string|\BoxedCode\Tracking\TrackableResourceModel $mixed
     */
    public function resource($mixed)
    {
        $model = $this->sourceModel($mixed);

        $instance = $this->container->make($model->type);

        $instance->setModel($model);

        return $instance;
    }

    /**
     * Dynamically pass calls to tracker instances.
     *
     * @param $name
     * @param array $arguments
     * @return \BoxedCode\Tracking\Contracts\Tracker
     */
    public function __call($name, $arguments = [])
    {
        if (array_key_exists($name, $this->trackers)) {
            $tracker = $this->container->make($this->trackers[$name]);

            $attr = $tracker->getModelAttributes($arguments);

            $tracker->setModel(TrackableResourceModel::create($attr));

            return $tracker;
        }
    }
}
