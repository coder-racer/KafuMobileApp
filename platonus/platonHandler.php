<?php

define("DEBUG", false);

if (DEBUG) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

class Handler
{
    private $advance;

    public function __construct()
    {
        require_once 'Platonus.php';
        $this->advance = new Platonus();
    }

    public function Route($action)
    {
        $method = $action . 'Action';
        if (method_exists($this->advance, $method)) {
            $this->advance->$method();
        }

        if ($this->getData('demo'))
        $this->renderHTML();

        $this->renderJSON();
    }

    public function renderHTML()
    {
        header('Content-Type: text/html; charset=utf-8');
        echo '<pre>';
        var_dump($this->advance->getResponseArray());
        echo '</pre>';
        die();
    }

    public function renderJSON()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $this->advance->getResponseJSON();
        die();
    }

    public function getData($key)
    {
        if (isset($_POST[$key]))
            return $_POST[$key];
        if (isset($_GET[$key]))
            return $_GET[$key];

        return false;
    }
}

$object = new Handler();

$object->Route($object->getData('act'));

