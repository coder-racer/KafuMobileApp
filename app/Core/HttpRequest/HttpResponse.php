<?php

namespace Core\HttpRequest;

class HttpResponse
{
    public function __construct(
        private $content,
        private $headers,
        private $cookies = []
    )
    {
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getJson()
    {
        return json_decode($this->content, true);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getCookie($name): bool|string
    {
        if (isset($this->cookies[$name]))
            return $this->cookies[$name];

        return false;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }
}