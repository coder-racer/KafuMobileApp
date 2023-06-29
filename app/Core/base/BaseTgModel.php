<?php

namespace Core\Base;
class BaseTgModel
{
    public $chat_id = null;

    public function __construct()
    {
        if (isset($this->request()['message']['chat']['id']))
            $this->chat_id = $this->request()['message']['chat']['id'];
        if (isset($this->request()['callback_query']['message']['chat']['id']))
            $this->chat_id = $this->request()['callback_query']['message']['chat']['id'];
    }

    public function getMessage()
    {
        if (isset($this->request()['message']['text']))
            return $this->request()['message']['text'];
        if (isset($this->request()['callback_query']['message']['text']))
            return $this->request()['callback_query']['message']['text'];
        return '';
    }

    public function checkCallBack(): bool
    {
        if (isset($this->request()['callback_query']))
            return true;
        return false;
    }

    public function getCallBackKey()
    {
        if ($this->checkCallBack())
            return $this->request()['callback_query']['data'];
        return '';
    }

    public function request(): mixed
    {
        static $res = null;
        if (is_null($res))
            $res = json_decode(file_get_contents('php://input'), true);
        return $res;
    }

    public function sendMessage($message, $keyboard = null, $inline = false): void
    {
        $keyboardDescription = "";
        $keyboardKey = 'keyboard';
        if ($keyboardKey)
            $keyboardKey = 'inline_keyboard';
        $data = array(
            'chat_id' => $this->chat_id,
            'text' => $message . $keyboardDescription,
            'reply_markup' => $keyboard ? json_encode([$keyboardKey => $keyboard]) : null
        );

        $this->callTelegramAPI('sendMessage', $data);
    }

    public function callTelegramAPI($method, $data): false|string
    {
        $apiUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_TOKEN') . '/' . $method;

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($apiUrl, false, $context);

        return $result;
    }
}