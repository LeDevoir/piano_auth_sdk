<?php

namespace LeDevoir\PianoAuthSDK\Client;

use LeDevoir\PianoAuthSDK\Response\LogoutResponse;
use LeDevoir\PianoAuthSDK\Response\TokenResponse;

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
     * @return TokenResponse
     * @throws \Exception
     */
    public function generateToken(string $email): TokenResponse
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/generateToken/email');

        $responseData = $this->post(
            $url,
            $this->port,
            [
                'email' => $email
            ],
            $this->defaultHeaders
        );

        return new TokenResponse($responseData);
    }

    /**
     * @param string $accessToken
     * @return LogoutResponse
     * @throws \Exception
     */
    public function logout(string $accessToken): LogoutResponse
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/logout');

        $responseData = $this->post(
            $url,
            $this->port,
            [
                'token' => $accessToken
            ],
            $this->defaultHeaders
        );

        return new LogoutResponse($responseData);
    }

    /**
     * Send a non-secure POST (no TLS)
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param int $port
     * @return array
     */
    private function post(
        string $url,
        int $port,
        array $data = [],
        array $headers = []
    ): array {
        try {
            $curl = curl_init();

            curl_setopt_array($curl,
                [
                    CURLOPT_URL => $url,
                    CURLOPT_PORT =>  $port,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_CONNECTTIMEOUT => 3,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false
                ]
            );

            $response = curl_exec($curl);

            if (!$response) {
                return [
                    'error' => [
                        'message' => 'request failed at curl execution',
                        'code' => 666,
                        'context' => [
                            'url' => $url,
                            'port' => $port
                        ]
                    ]
                ];
            }

            $data = json_decode($response, true) ?? [];

            if(!array_key_exists('error', $data) && !array_key_exists('data', $data)) {
                return [
                    'error' => [
                        'message' => 'response data not matching expected error and success format (should start with a first-level key error or success)',
                        'code' => 666,
                        'context' => [
                            'url' => $url,
                            'port' => $port,
                            'response_content' => $response
                        ]
                    ]
                ];
            }

            return $data;
        } catch (\Exception $exception) {
            throw $exception;
        } finally {
            curl_close($curl);
        }
    }
}