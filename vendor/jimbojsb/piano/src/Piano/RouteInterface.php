<?php
namespace Piano;

interface RouteInterface
{
    public function match(Request $request);
    public function getCallback();
    public function getParams();
}