<?php

namespace Core;

use stdClass;

class Request
{
    protected array $get;
    protected array $post;
    protected object $parameters;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->parameters = (object)array_merge($_GET, $_POST);
    }

    public function __get($name)
    {
        return $this->parameters->$name ?? null;
    }


    // Получить все параметры запроса в виде массива
    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    // Получить все параметры запроса в виде объекта
    public function allAsObject(): stdClass
    {
        return $this->parameters;
    }

    // Получить параметр запроса по имени
    public function input($name, $default = null)
    {
        return $this->parameters->$name ?? $default;
    }

    // Проверить, есть ли указанный параметр в запросе
    public function has($name): bool
    {
        return isset($this->parameters->$name);
    }
}
