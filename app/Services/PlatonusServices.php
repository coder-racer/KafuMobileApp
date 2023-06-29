<?php

namespace Services;

use Core\HttpRequest\HttpRequest;

class PlatonusServices
{
    private string|null $url = null;

    public function __construct()
    {
        $this->url = env('PLATONUS_URL');
    }

    public function loginAction($login, $password): array
    {

        $http = new HttpRequest($this->url . 'rest/api/login');

        $http->setHeader('Content-Type', 'application/json');

        $response = $http->post(["login" => $login, "iin" => null, "password" => $password], true);

        $JSESSIONID = $response->getCookie("JSESSIONID");

        $result = $response->getJson();

        if ($result['login_status'] == 'success') {
            $token = $result['auth_token'];
            return
                [
                    'res' => true,
                    'data' => [
                        'token' => $token,
                        'JSESSIONID' => $JSESSIONID,
                        'userId' => $this->getUserId($token)
                    ],
                ];
        } else {
            return ['res' => false, 'data' => $result['message']];
        }
    }

    public function getUserDataAction($token)
    {
        $http = new HttpRequest($this->url . 'rest/mobile/personInfo/ru');
        $http->setHeader('token', $token);

        return $http->get()->getJson();
    }

    private function getUserId($token)
    {
        return $this->getUserDataAction($token)['studentID'];
    }

    public function getJournalAction($year, $academic, $JSESSIONID, $token, $userId)
    {
        $http = new HttpRequest($this->url . 'journal/' . $year . '/' . $academic . '/' . $userId);
        $http->setHeader('token', $token);
        $http->setCookie('JSESSIONID', $JSESSIONID);
        $http->setCookie('sessionid', $JSESSIONID);

        $response = $http->get();

        return $response->getJson();
    }

}