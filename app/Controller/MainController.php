<?php

namespace Controller;


use Core\App;
use Models\AuthModel;
use Models\ContestModel;
use Models\UserCodeModel;
use PDO;
use TgModels\UserTgModel;

class MainController
{
    public function index(): string
    {
        return view('index');
    }
}