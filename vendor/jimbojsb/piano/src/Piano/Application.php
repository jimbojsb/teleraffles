<?php
namespace Piano;

use \Piano\Dispatcher,
    \Piano\Request,
    \Piano\Router;

class Application
{
    protected $resourceManager;

    public function __construct()
    {
        $this->resourceManager = new \Piano\ResourceManager();
        $this->addResource('router', new Router());
        $this->addResource('dispatcher', new Dispatcher($this));

    }

    public function run()
    {
        $request = new Request($_SERVER);
        $response = $this->getResource('dispatcher')->dispatch($request);
        echo $response;
    }

    protected  function getResource($resource)
    {
        return $this->resourceManager->getResource($resource);
    }

    protected function addResource($name, $resource)
    {
        $this->resourceManager->addResource($name, $resource);
    }

    public function __get($property)
    {
        return $this->getResource($property);
    }

    public function __set($property, $value)
    {
        $this->addResource($property, $value);
    }

}