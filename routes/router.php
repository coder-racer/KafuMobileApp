<?php

use Controller\ApiController;
use Controller\PlatonusController;
use Controller\TelegramController;
use Core\Router;
use Controller\MainController;


Router::setDefaultController([MainController::class, 'index']);


Router::any('/api/getUserData', [PlatonusController::class, 'getUserData']);
Router::any('/api/getJournal', [PlatonusController::class, 'getJournal']);
Router::any('/api/getNews', [PlatonusController::class, 'getNews']);
Router::any('/api/login', [PlatonusController::class, 'login']);