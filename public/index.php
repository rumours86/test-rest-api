<?php

ini_set('display_errors', true);
define('PROJECT_DIR', dirname(__DIR__));
require_once PROJECT_DIR . '/config/bootstrap.php';
$app->run();
