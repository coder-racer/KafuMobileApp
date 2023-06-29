<?php
require_once '../app/сore/App.php';
require_once '../app/сore/Utils.php';
require_once '../vendor/autoload.php';

if (env("DEBUG")) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_PARSE);
}

use Controller\MainController;
use Core\App;
use Core\Router;


$app = new App();
