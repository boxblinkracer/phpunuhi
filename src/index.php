<?php

declare(strict_types=1);

use PHPUnuhi\AppManager;
use Symfony\Component\Dotenv\Dotenv;

/** @phpstan-ignore-next-line */
require_once "phar://phpunuhi.phar/vendor/autoload.php";

$envFile = getcwd() . '/.env';
if (file_exists($envFile)) {
    (new Dotenv())->usePutenv()->loadEnv($envFile);
}

AppManager::run();
