<?php

if (!defined('PROJECT_DIR')) {
    define('PROJECT_DIR', dirname(__DIR__));
}

require_once PROJECT_DIR . '/vendor/autoload.php';

$app = new App\System\App();
