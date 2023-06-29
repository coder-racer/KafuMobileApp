<?php

namespace Controller;


use Core\App;
use Core\Request;
use Models\AuthModel;
use Models\ContestModel;
use Models\UserCodeModel;
use PDO;
use Services\TestServices;
use TgModels\UserTgModel;

class MainController
{
    public function index(): string
    {
        return view('index');
    }
}