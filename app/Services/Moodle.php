<?php

namespace Services;

class Moodle
{
    private string $token;
    private string $url;

    public function __construct()
    {
        $this->token = env('MOODLE_TOKEN');
        $this->url = env('MOODLE_URL');
    }

    public function getGrade($login, $password)
    {
        return $this->moodleRequest('local_get_user_grades_get_grades',
        [
            'username' => $login,
            'password' => $password,
        ]);
    }

    private function moodleRequest($act, $params = [])
    {
        $url = $this->url . '/webservice/rest/server.php?';
        $params['wstoken'] = $this->token;
        $params['moodlewsrestformat'] = 'json';
        $params['wsfunction'] = $act;

            // Инициализируем cURL
        $ch = curl_init($url);

        // Устанавливаем параметры запроса
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Отправляем запрос и сохраняем ответ
        $response = curl_exec($ch);

        // Закрываем сессию cURL
        curl_close($ch);

        // Возвращаем ответ
        return json_decode($response, true);
    }

}