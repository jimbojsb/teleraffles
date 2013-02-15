<?php
namespace Piano;

class ResourceManager
{
    private $resources;
    private $lazyResources;

    public function addResource($name, $resource)
    {
        if ($resource instanceof \Closure) {
            $this->lazyResources[$name] = $resource;
        } else {
            $this->resources[$name] = $resource;
        }
    }

    public function getResource($name)
    {
        if (!isset($this->resources[$name]) && isset($this->lazyResources[$name])) {
            $this->resources[$name] = $this->lazyResources[$name]();
        }
        return $this->resources[$name];
    }
}