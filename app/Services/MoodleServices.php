<?php

namespace Services;


use core\HttpRequest\HttpRequest;

class MoodleServices
{
    private string $token;
    private string $url;

    public function __construct(private readonly HttpRequest $http)
    {
        $this->token = env('MOODLE_TOKEN');
        $this->url = env('MOODLE_URL');

        $this->http->setUrl($this->url . '/webservice/rest/server.php?');
    }

    public function getGrade(string $login, string $password): array
    {
        return $this->moodleRequest(
            'local_get_user_grades_get_grades',
            [
                'username' => $login,
                'password' => $password,
            ]);
    }

    private function moodleRequest(string $function, array $params = []): array
    {
        $params['wstoken'] = $this->token;
        $params['moodlewsrestformat'] = 'json';
        $params['wsfunction'] = $function;

        return $this->http->post($params)->getJson();
    }

}