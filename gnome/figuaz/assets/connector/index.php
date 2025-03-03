<?php

define('JODIT_DEBUG', false);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Application.php';

$config = (array) require __DIR__ . '/default.config.php';

if (file_exists(__DIR__ . '/config.php')) {
    $con = (array) require __DIR__ . '/config.php';
    $config = array_merge($config, $con);
}

$fileBrowser = new \JoditRestApplication($config);

try {
    $fileBrowser->checkAuthentication();
    $fileBrowser->execute();
} catch (\ErrorException $e) {
    $fileBrowser->exceptionHandler($e);
}

