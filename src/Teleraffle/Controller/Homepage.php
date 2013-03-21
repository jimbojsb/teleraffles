<?php
namespace Teleraffle\Controller;

use Piano\View,
    Piano\Response;

class Homepage
{
    public function index()
    {
        return (new View)->render('index.phtml');
    }

    public function error()
    {
        return (new Response("404 Not Found"))->setStatusCode(404);
    }
}