<?php
namespace Piano;

use \Piano\Route,
    \Piano\Request;

class Router implements \ArrayAccess
{
    protected $routes = array();
    protected $notfoundHandler;
    protected $errorHandler;

    public function setErrorHandler($error)
    {
        $r = new Route();
        $r(null, $error);
        $this->errorHandler = $r;
    }

    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    public function setNotfoundHandler($notfound)
    {
        $r = new Route();
        $r(null, $notfound);
        $this->notfoundHandler = $r;
    }

    public function getNotfoundHandler()
    {
        return $this->notfoundHandler;
    }

    public function route(Request $request)
    {
        foreach ($this->routes as $name => $route) {
            if ($route->match($request)) {
                return array(
                    'callback' => $route->getCallback(),
                    'params'   => $route->getParams()
                );
            }
        }
    }

    public function addRoute($name, RouteInterface $route)
    {
        $this->routes[$name] = $route;
    }

    public function offsetExists($offset)
    {
        return $this->routes[$offset];
    }

    /**
     * @param mixed $offset
     * @return \Piano\Route
     */
    public function offsetGet($offset)
    {
        if (!isset($this->routes[$offset])) {
            $this->routes[$offset] = new Route();
        }
        return $this->routes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->routes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->routes[$offset]);
    }


}