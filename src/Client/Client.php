<?php

namespace LeDevoir\PianoAuthSDK\Client;

class Client
{
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var int
     */
    private $port;
    /**
     * @var array|string[]
     */
    private $defaultHeaders;

    public function __construct(string $baseUrl, int $port)
    {
        $this->baseUrl = $baseUrl;
        $this->port = $port;
        $this->defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * Generate a Piano user access token by email
     *
     * @param string $email
     * @return bool|string
     * @throws \Exception
     */
    public function generateToken(string $email)
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/generateToken/email');

        return $this->post(
            $url,
            $this->port,
            [
                'email' => $email
            ],
            $this->defaultHeaders
        );
    }

    /**
     * @param string $accessToken
     * @return bool|string
     */
    public function logout(string $accessToken)
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/logout');

        return $this->post(
            $url,
            $this->port,
            [
                'token' => $accessToken
            ],
            $this->defaultHeaders
        );
    }

    /**
     * Send a non-secure POST (no TLS)
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $port
     * @return bool|string
     */
    private function post(
        string $url,
        int $port,
        array $data = [],
        array $headers = []
    ){
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_PORT, $port);

            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,3);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($curl);
            if (curl_error($curl)) {
                /** Do something here ? */
            }

            return $response;
        } catch (\Exception $exception) {
            throw $exception;
        } finally {
            curl_close($curl);
        }
    }
}