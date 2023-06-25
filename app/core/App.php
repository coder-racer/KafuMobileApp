<?php

namespace Core;

use Controller\MainController;
use Core\Base\BaseModel;
use Exception;
use PDO;

class App
{
    private PDO $pdo;
    private static ?App $instance = null;

    public static function getPDO(): PDO
    {
        return self::$instance->pdo;
    }

    public function __construct()
    {
        session_start();
        self::$instance = $this;
        require_once 'Autoloader.php';
        require_once 'Router.php';

        $autoLoader = new Autoloader();
        $autoLoader->register();

        if ((bool)env('DB_ON')) {
            $this->pdo = new PDO(
                "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4",
                env('DB_USER'),
                env('DB_PASSWORD')
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        $router = new Router();
        include_once baseDir() . "/routes/router.php";

        $router->run();

    }

}