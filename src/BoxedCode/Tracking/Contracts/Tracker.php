<?php

namespace BoxedCode\Tracking\Contracts;

use BoxedCode\Tracking\TrackableResourceModel;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

interface Tracker
{
    /**
     * Tracker constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Container $container, Dispatcher $events, Repository $config);

    /**
     * Get the type handle.
     *
     * @return string
     */
    public function getHandle();

    /**
     * Get the route name.
     *
     * @return string
     */
    public function getRouteName();

    /**
     * Get the route parameter key.
     *
     * @return string
     */
    public function getRouteKey();

    /**
     * Set the data model.
     *
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return $this
     */
    public function setModel(TrackableResourceModel $model);

    /**
     * Get the data model.
     *
     * @return \BoxedCode\Tracking\TrackableResourceModel
     */
    public function getModel();

    /**
     * Set the routing parameter.
     *
     * @param $parameter
     * @return $this
     */
    public function setRoutingParameter($parameter);

    /**
     * Get the routing parameter.
     *
     * @return string
     */
    public function getRoutingParameter();

    /**
     * Register the trackers route.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function registerRoute(Router $router);

    /**
     * Handle the tracking request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \BoxedCode\Tracking\TrackableResourceModel $model
     * @return mixed
     */
    public function handle(Request $request, TrackableResourceModel $model);

    /**
     * Generate a model attribute array from an argument array.
     *
     * @param array $args
     * @return array
     */
    public function getModelAttributes(array $args);

    /**
     * Get a 'trackable' url for the current data model.
     *
     * @param array $parameters
     * @return \League\Url\Url
     */
    public function getTrackedUrl($parameters = []);

    /**
     * Get a string representation of the tracker, the 'trackable' url.
     * @return string
     */
    public function __toString();
}
