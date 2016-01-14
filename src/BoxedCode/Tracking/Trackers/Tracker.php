<?php

namespace BoxedCode\Tracking\Trackers;

use BoxedCode\Tracking\TrackableResourceModel;
use BoxedCode\Tracking\TrackedEvent;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use League\Url\Url;

abstract class Tracker
{
    /**
     * Container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Tracker data model.
     *
     * @var \BoxedCode\Tracking\TrackableResourceModel
     */
    protected $model;

    /**
     * Configuration repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The trackers type handle.
     *
     * @var string
     */
    protected $handle;

    /**
     * The trackers route name.
     *
     * @var string
     */
    protected $route_name;

    /**
     * The trackers route parameter.
     *
     * @var string
     */
    protected $route_parameter;

    /**
     * Tracker constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Container $container, Dispatcher $events, Repository $config)
    {
        $this->container = $container;

        $this->events = $events;

        $this->config = $config;
    }

    /**
     * Get the type handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get the route name.
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->route_name;
    }

    /**
     * Get the route parameter key.
     *
     * @return string
     */
    public function getRouteKey()
    {
        return $this->route_parameter;
    }

    /**
     * Set the data model.
     *
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return $this
     */
    public function setModel(TrackableResourceModel $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the data model.
     *
     * @return \BoxedCode\Tracking\TrackableResourceModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the routing parameter.
     *
     * @param $parameter
     * @return $this
     */
    public function setRoutingParameter($parameter)
    {
        $this->route_parameter = $parameter;

        return $this;
    }

    /**
     * Get the routing parameter.
     *
     * @return string
     */
    public function getRoutingParameter()
    {
        return $this->route_parameter;
    }

    /**
     * Get the routing path.
     *
     * @return string
     */
    protected function getRoutingPath()
    {
        $path = str_finish($this->config->get('tracking.path'), '/');

        return $path.$this->route_parameter.'/{id}';
    }

    /**
     * Register the trackers route.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function registerRoute(Router $router)
    {
        $path = $this->getRoutingPath();

        $router->get($path, ['as' => $this->route_name, function (Request $request, $id) {
            if (! ($model = TrackableResourceModel::find($id))) {
                abort(404);
            }

            $this->events->fire('tracking.tracked', new TrackedEvent($model, $request));

            return $this->handle($request, $model);
        }]);
    }

    /**
     * Handle the tracking request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return mixed
     */
    abstract public function handle(Request $request, TrackableResourceModel $model);

    /**
     * Generate a model attribute array from an argument array.
     *
     * @param array $args
     * @return array
     */
    public function getModelAttributes(array $args)
    {
        $attrs = [
            'id' => $this->getUniqueId(),
            'type' => get_class($this),
        ];

        return array_merge($args, $attrs);
    }

    /**
     * Get a 'trackable' url for the current data model.
     *
     * @param array $parameters
     * @return \League\Url\Url
     */
    public function getTrackedUrl($parameters = [])
    {
        $resolver = $this->config->get('tracking.resolver', 'route');

        $route_url = call_user_func_array($resolver, [$this->route_name, $this->model->getKey()]);

        $url = Url::createFromUrl($route_url);

        $url->getQuery()->modify($parameters);

        return $url;
    }

    /**
     * Get a unique model id.
     *
     * @return string
     */
    protected function getUniqueId()
    {
        while (! isset($id) || TrackableResourceModel::find($id)) {
            $id = str_random(6);
        }

        return $id;
    }

    /**
     * Get a string representation of the tracker, the 'trackable' url.
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTrackedUrl();
    }
}
