<?php
require_once __DIR__ . '/../vendor/autoload.php';
$application = new \Piano\Application();

require_once __DIR__ . '/routes.php';

$application->redis = new Predis\Client(null, ['prefix' => 'tlr:']);
\Piano\View::setBathPath(__DIR__ . '/../resources/views');

$application->run();