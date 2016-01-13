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
    protected $container;

    protected $model;

    protected $config;

    protected $events;

    protected $handle;

    protected $route_name;

    protected $route_key;

    public function __construct(Container $container, Dispatcher $events, Repository $config)
    {
        $this->container = $container;

        $this->events = $events;

        $this->config = $config;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    public function getRouteName()
    {
        return $this->route_name;
    }

    public function getRouteKey()
    {
        return $this->route_key;
    }

    public function setModel(TrackableResourceModel $model)
    {
        $this->model = $model;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setRoutingParameter($parameter)
    {
        $this->route_key = $parameter;

        return $this;
    }

    public function getRoutingParameter()
    {
        return $this->route_key;
    }

    protected function getRelativePath()
    {
        $path = str_finish($this->config->get('tracking.path'), '/');

        return $path.$this->route_key;
    }

    public function registerRoute(Router $router)
    {
        $path = $this->getRelativePath().'/{id}';

        $router->get($path, ['as' => $this->route_name, function (Request $request, $id) {
            if (! ($model = TrackableResourceModel::find($id))) {
                abort(404);
            }

            $this->events->fire('tracking.tracked', new TrackedEvent($model, $request));

            return $this->handle($request, $model);
        }, ]);
    }

    abstract public function handle(Request $request, TrackableResourceModel $model);

    public function validateArguments(array $args)
    {
        return true;
    }

    public function transformArguments(array $args)
    {
        return $args;
    }

    public function getTrackedUrl($parameters = [])
    {
        $route_url = route($this->route_name, $this->model->getKey());

        $url = Url::createFromUrl($route_url);

        $url->getQuery()->modify($parameters);

        return $url;
    }

    public function __toString()
    {
        return (string) $this->getTrackedUrl();
    }
}
