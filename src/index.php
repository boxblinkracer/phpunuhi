<?php

use PHPUnuhi\AppManager;
use Symfony\Component\Dotenv\Dotenv;

require_once "phar://phpunuhi.phar/vendor/autoload.php";

$envFile = getcwd() . '/.env';
if (file_exists($envFile)) {
    (new Dotenv())->usePutenv()->loadEnv($envFile);
}

AppManager::run();
