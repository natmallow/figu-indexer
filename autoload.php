<?php

// Check if SERVER_NAME is set
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

// Set HTTP_ENVIRONMENT based on SERVER_NAME
if ($_SERVER['SERVER_NAME'] == 'dev.figuindexer.org' || 
    $_SERVER['SERVER_NAME'] == 'dev.figuarizona.org' || 
    $_SERVER['SERVER_NAME'] == 'devapi.figuarizona.org' || 
    $_SERVER['SERVER_NAME'] == 'localhost' ) {
    $_SERVER['HTTP_ENVIRONMENT'] = 'dev';
} else {
    $_SERVER['HTTP_ENVIRONMENT'] = 'prod';
}

// Include functions.php
require_once(__DIR__ . '/includes/crystal/functions.php');

// Autoload vendor classes
function vendorAutoLoader() {
    require_once __DIR__ . '/vendor/composer/autoload_real.php';
    return ComposerAutoloaderInit41229c735da97d41f32abbfd33a7439e::getLoader();
}

// Custom autoloader for organization-specific classes
function orgAutoloader($name) {
    $ignorelist = array('assets\\', 'models\\', 'phpmailer\\');
    $lowercase_name = $name;

    $aIgnore = array_filter($ignorelist, function ($element) use ($lowercase_name) {
        return strpos($lowercase_name, $element) !== FALSE;
    });

    if (empty($aIgnore)) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", "/", $lowercase_name) . ".php";
        $file = str_replace("/", DIRECTORY_SEPARATOR,  $file);
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

spl_autoload_register('vendorAutoLoader');
spl_autoload_register('orgAutoloader');

// Initialize security instance
$SECURITY = gnome\classes\Security::getInstance();

// Initialize language (assuming lang() function is defined in functions.php)
$LANG = lang();
