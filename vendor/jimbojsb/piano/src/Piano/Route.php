<?php
namespace Piano;

use \Piano\Request;

class Route implements RouteInterface
{
    protected $method;
    protected $path;
    protected $callback;
    protected $params = array();
    protected $matcher;

    public function getCallback()
    {
        return $this->callback;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function call(callable $matcher)
    {
        $this->matcher = $matcher;
        unset($this->path);
        unset($this->callback);
        unset($this->params);
        unset($this->method);
    }

    public function __invoke($route, $callback, $params = array())
    {
        if ($route) {
            list($method, $path) = explode(' ', $route);
            if (strpos($method, ',') !== false) {
                $method = explode(',', $method);
            }
            $this->path = $path;
            $this->method = $method;
        } else {
            $this->method = null;
            $this->path = null;
        }
        $this->callback = $callback;
        $this->params = $params;
    }

    public function match(Request $request)
    {
        if ($this->matcher) {

        } else {
            $pathMatches = false;
            $paramSearchRegex = '`:[a-z]+`';
            $paramReplacementRegex = '(.+?)';
            $paramNames = array();
            $pathRegex = preg_replace_callback($paramSearchRegex, function($matches) use (&$paramNames, $paramReplacementRegex) {
                $paramNames[] = str_replace(':', '', $matches[0]);
                return $paramReplacementRegex;
            }, $this->path);
            if (preg_match("`^$pathRegex$`", $request->getPath(), $matches)) {
                if (is_string($this->method) && ($this->method === $_SERVER['REQUEST_METHOD'])) {
                    $pathMatches = true;
                } else if ($this->method === null) {
                    $pathMatches = true;
                } else if (is_array($this->method) && in_array($_SERVER['REQUEST_METHOD'], $this->method)) {
                    $pathMatches = true;
                }
            }

            if ($pathMatches) {
                $paramValues = array_slice($matches, 1);
                array_walk($paramValues, function(&$val) {
                    $val = urldecode($val);
                });
                $params = array_combine($paramNames, $paramValues);
                $this->params = array_merge($this->params, $params);
                return true;
            }
        }
    }
}