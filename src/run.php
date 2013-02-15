<?php
$application = new Piano\Application();

require_once __DIR__ . '/routes.php';

$application->run();