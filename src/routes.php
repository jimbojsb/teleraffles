<?php
/** @var $router Piano\Router */
$router = $application->router;

$router['homepage']('GET /', 'Teleraffle\Controller\Homepage.index');
$router['create']('GET,POST /create', 'Teleraffle\Controller\Raffle.create');
$router['view']('GET /view/:id', 'Teleraffle\Controller\Raffle.view');
$router['winners']('GET /winners/:id', 'Teleraffle\Controller\Raffle.winners');
$router['sms']('POST /sms', 'Teleraffle\Controller\Sms.receive');

$router->setNotfoundHandler('Teleraffle\Controller\Homepage.error');