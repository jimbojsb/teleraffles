<?php
namespace Piano;

class Exception extends \Exception
{
    const CODE_ROUTE_NO_MATCH = 10;
    const CODE_ROUTE_ACL_DENIED = 11;
    const CODE_ROUTE_NO_CONTROLLER = 12;

    const MESSAGE_ROUTE_NO_MATCH = "No matching route found";
    const MESSAGE_ROUTE_ACL_DENIED = "Route matched but ACL denied access";
    const MESSAGE_ROUTE_NO_CONTROLLER = "Controller not found";
}