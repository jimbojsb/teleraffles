<?php
spl_autoload_register(function($className) {
    if (strpos($className, "Teleraffle\\") === 0) {
        $fileName = str_replace("\\", '/', $className) . ".php";
        require_once __DIR__ . '/' . $fileName;
    }
});