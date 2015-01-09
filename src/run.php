<?php
require_once __DIR__ . '/../vendor/autoload.php';
$application = new \Piano\Application();

require_once __DIR__ . '/routes.php';

$application->db = new \Aura\Sql\ExtendedPdo("mysql:host=localhost;dbname=teleraffles", "teleraffles", "teleraffles");
\Piano\View::setBathPath(__DIR__ . '/../resources/views');

$application->run();