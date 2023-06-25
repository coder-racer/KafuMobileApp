<?php

namespace Services;
class Platonus
{
    private string|null $url = null;
    private string|null $token;
    private string|null $JSESSIONID;
    private string|null $userData;
    private string|null $journal;
    private string|null $news;
    private string|null $news5;

    public function __construct()
    {
        $this->url = env('PLATONUS_URL');
    }

    public function getData($key)
    {
        return $_POST[$key] ?? ($_GET[$key] ?? false);
    }

    public function loginAction()
    {
        $url = $this->url . 'rest/api/login';
        $data = ["login" => $this->getData('login'), "iin" => null, "password" => $this->getData('pass')];
        $data_string = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $this->JSESSIONID = $this->getJSESSIONID(substr($response, 0, $header_size));
        $result = substr($response, $header_size);
        curl_close($curl);
        $result = json_decode($this->utf8_unescape($result), true);
        if ($result['login_status'] == 'success') {
            $this->token = $result['auth_token'];
//            $this->getUserData();
//            $this->getJournal();
            return
                [
                    'res' => true,
                    'data' => [
                        'token' => $this->token,
                        'JSESSIONID' => $this->JSESSIONID,
                        'userId' => $this->getUserId()
                    ],

                ];
        } else {
            return (['res' => false, 'data' => $result['message']]);
        }
    }

    public function getNewsAction()
    {
        $this->news();
        return ['data' => $this->news];
    }


    public function getUserDataAction()
    {
        $url = $this->url . 'rest/mobile/personInfo/ru';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'token: ' . $this->getData('token')
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $result = substr($response, $header_size);

        curl_close($curl);


        $result = json_decode($this->utf8_unescape($result), true);

        return $result;
    }

    private function getUserId()
    {
        $url = $this->url . 'rest/mobile/personInfo/ru';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'token: ' . $this->token
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $result = substr($response, $header_size);

        curl_close($curl);


        $result = json_decode($this->utf8_unescape($result), true);

        // unset($result['photoBase64']);

        return $result['studentID'];
    }

    private function getJSESSIONID($str)
    {
        $headers = array();
        $headersTmpArray = explode("\r\n", $str);
        for ($i = 0; $i < count($headersTmpArray); ++$i) {
            if (strlen($headersTmpArray[$i]) > 0) {
                if (strpos($headersTmpArray[$i], ":")) {
                    $headerName = substr($headersTmpArray[$i], 0, strpos($headersTmpArray[$i], ":"));
                    $headerValue = substr($headersTmpArray[$i], strpos($headersTmpArray[$i], ":") + 1);
                    if ($headerName != 'Set-Cookie') {
                        $headers[$headerName] = trim($headerValue);
                    } else {
                        foreach (explode(';', trim($headerValue)) as $cookie) {
                            $item = explode('=', $cookie);
                            if ($item[0] == 'JSESSIONID') {
                                return $cookie;
                            }
                        }
                    }
                }
            }
        }
        return $headers;
    }

    public function getJournalAction()
    {
        $year = $this->getData('year');
        $academic = $this->getData('academic');
        $JSESSIONID = $this->getData('JSESSIONID');
        $token = $this->getData('token');
        $userId = $this->getData('userId');


        $url = $this->url . 'journal/' . $year . '/' . $academic . '/' . $userId;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Cookie: ' . $JSESSIONID . ';' . str_replace('JSESSIONID', 'sessionid', $JSESSIONID),
                'Content-Type: application/json;charset=utf-8',

                'token: ' . $token
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $result = substr($response, $header_size);

        curl_close($curl);

        return json_decode($this->utf8_unescape($result), true);
    }

    private function news($page = 1)
    {

        $page = ($page - 1) * 5;

        $url = "https://kafu.edu.kz/news/page/" . $page;
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


        $resp = curl_exec($curl);
        curl_close($curl);

        $this->news = file_get_contents("https://kafu.edu.kz/news/page/" . $page);// str_replace("\n", '', $resp);
        $this->news = str_replace("\r", '', $this->news);
        $this->news = str_replace("\t", '', $this->news);
    }


    private function utf8_unescape($input)
    {
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function ($a) {
                return mb_chr(hexdec($a[1]));
            },
            $input
        );
    }

}