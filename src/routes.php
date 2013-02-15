<?php
/** @var $router Piano\Router */
$router = $application->router;

$router['homepage']('GET /', '\Teleraffle\Controller\Homepage');