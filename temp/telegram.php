<?php

require_once 'SafeMySQL.php';

class TelegramBot
{
    private $db;
    private $user = null;
    private $chat_id = null;

    private $token = '6029104041:AAGLD6cvhloCUF7IsIKqm2gRUZCkO-U7H_Y';

    private function getUser()
    {
        $this->user = $this->db->getRow("SELECT * FROM users WHERE chat_id = ?s", $this->chat_id);
        if (!$this->user) {
            $data = array(
                'chat_id' => $this->chat_id,
            );

            $this->db->query("INSERT INTO users SET ?u", $data);
            $this->user = $this->db->getRow("SELECT * FROM users WHERE chat_id = ?s", $this->chat_id);
        }
    }

    public function __construct()
    {
        $this->db = new SafeMySQL(array(
            'user' => 'adminrh0_sdfsdf',
            'pass' => 'oz%Kr8BL',
            'db' => 'adminrh0_sdfsdf',
            'host' => 'localhost'
        ));
    }

    public function processMessage($message)
    {
        $this->chat_id = $message['chat']['id'];
        $this->getUser();
        $this->sendMessage(json_encode($this->user));

//        $text = $message['text'];

//        switch ($text) {
//            case '/start':
//                $this->sendMessage($chatId, 'Добро пожаловать! Пожалуйста, введите свой номер телефона в формате +7XXXXXXXXXX:');
//                break;
//            case '/stop':
//                $this->sendMessage($chatId, 'До свидания! Если у вас возникнут вопросы, обратитесь к администратору.');
//                break;
//            default:
//                $this->registerParticipant($chatId, $text);
//                break;
//        }
    }

    private function registerParticipant($chatId, $phone)
    {
        // Здесь можно добавить дополнительные проверки и валидацию номера телефона

        $keyboard = [
            'gender' => [
                ['Мужской', 'Женский']
            ],
            'age' => [
                ['18-23', '23-30'],
                ['31-40', '41-50'],
                ['51-60', '61 и более']
            ],
            'city' => [
                ['Кокшетау', 'Костанай'],
                ['Петропавловск']
            ]
        ];

        $data = array(
            'chat_id' => $chatId,
            'phone' => $phone,
            'gender' => null,
            'age' => null,
            'city' => null
        );

        $this->db->query("INSERT INTO users SET ?u", $data);

        $this->sendMessage($chatId, 'Отлично! Теперь выберите ваш пол:', $keyboard['gender']);
    }

    private function sendMessage($message, $keyboard = null)
    {
        $data = array(
            'chat_id' => $this->chat_id,
            'text' => $message,
            'reply_markup' => $keyboard ? json_encode(['keyboard' => $keyboard, 'one_time_keyboard' => true, 'resize_keyboard' => true]) : null
        );

        $this->callTelegramAPI('sendMessage', $data);
    }

    private function callTelegramAPI($method, $data)
    {
        $apiUrl = 'https://api.telegram.org/bot' . $this->token . '/' . $method;

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

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$telegramBot = new TelegramBot();

$update = json_decode(file_get_contents('php://input'), true);
$telegramBot->processMessage($update['message']);

