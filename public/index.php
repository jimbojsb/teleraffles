<?php
if (php_sapi_name() == 'cli-server'
    && !in_array($_SERVER['REQUEST_URI'], ['/', 'index.php'])
    && file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return false;
}

ini_set('error_reporting', E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

require_once __DIR__ . '/../src/run.php';