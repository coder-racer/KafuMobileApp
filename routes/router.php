<?php

use Controller\KafuSiteController;
use Core\Router;
use Controller\MainController;
use Controller\MoodleController;
use Controller\DocumentController;
use Controller\PlatonusController;


Router::setDefaultController([MainController::class, 'index']);


Router::any('/api/getUserData', [PlatonusController::class, 'getUserData']);
Router::any('/api/getJournal', [PlatonusController::class, 'getJournal']);
Router::any('/api/getNews', [KafuSiteController::class, 'getNews']);
Router::any('/api/login', [PlatonusController::class, 'login']);

Router::any('/api/getGrade', [MoodleController::class, 'getGrade']);

Router::any('/getDocument', [DocumentController::class, 'getDocument']);
Router::any('/api/getListDocs', [DocumentController::class, 'getListDocs']);