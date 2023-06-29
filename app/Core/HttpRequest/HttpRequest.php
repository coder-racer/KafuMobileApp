<?php

namespace Core\HttpRequest;

class HttpRequest
{
    private string $url;
    private array $headers = array();
    private array $cookies = array();
    private array $responseCookies = array();
    private ?string $error = null;

    public function __construct($url = '')
    {
        $this->url = $url;
    }

    public function setUrl($url): static
    {
        $this->url = $url;
        return $this;
    }

    public function setHeader($headerName, $headerValue): static
    {
        $this->headers[] = "$headerName: $headerValue";
        return $this;
    }

    public function setCookie($cookieName, $cookieValue): static
    {
        $this->cookies[] = "$cookieName=$cookieValue";
        return $this;
    }

    private function execute($method, $data = array(), $customRequest = false): HttpResponse
    {
        $ch = curl_init();

        switch ($method) {
            case 'GET':
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_URL, $this->url . '?' . http_build_query($data));
                } else {
                    curl_setopt($ch, CURLOPT_URL, $this->url);
                }
                break;
            case 'POST':
                if($customRequest) {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    if (!empty($data)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $this->setHeader('Content-Length', strlen(json_encode($data)));
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POST, 1);
                    if (!empty($data)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    }
                }
                curl_setopt($ch, CURLOPT_URL, $this->url);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_URL, $this->url . '?' . http_build_query($data));
                } else {
                    curl_setopt($ch, CURLOPT_URL, $this->url);
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                curl_setopt($ch, CURLOPT_URL, $this->url);
                break;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $this->cookies));
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->error = 'Curl error: ' . curl_error($ch);
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($result, 0, $headerSize);
        $body = substr($result, $headerSize);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
        $cookies = $matches[1];
        $responseCookies = array();

        foreach ($cookies as $item) {
            parse_str($item, $cookie);
            $responseCookies = array_merge($responseCookies, $cookie);
        }

        curl_close($ch);

        return new HttpResponse($body, $headers, $responseCookies);
    }


    public function get($data = array()): HttpResponse
    {
        return $this->execute('GET', $data);
    }

    public function post($data, $customRequest = false): HttpResponse
    {
        return $this->execute('POST', $data, $customRequest);
    }

    public function delete($data = array()): HttpResponse
    {
        return $this->execute('DELETE', $data);
    }

    public function put($data): HttpResponse
    {
        return $this->execute('PUT', $data);
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}

?>
