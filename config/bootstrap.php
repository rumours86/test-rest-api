<?php

if (!defined('PROJECT_DIR')) {
    define('PROJECT_DIR', dirname(__DIR__));
}

require_once PROJECT_DIR . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
(new Dotenv(true))->loadEnv(PROJECT_DIR . '/.env');

$app = App\Kernel::getInstance();
