<?php
namespace Piano;

trait ControllerTrait
{
    protected $request;
    protected $application;

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function notfound()
    {
        return $this->application->dispatcher->notfound();
    }
}