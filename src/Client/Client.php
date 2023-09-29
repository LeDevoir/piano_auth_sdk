<?php

namespace LeDevoir\PianoAuthSDK\Client;

use Exception;
use LeDevoir\PianoAuthSDK\Responses\LogoutResponse;
use LeDevoir\PianoAuthSDK\Responses\TokenResponse;

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
     * @param int $profilId
     * @return TokenResponse
     * @throws Exception
     */
    public function generateToken(int $profilId): TokenResponse
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/generateToken/email');

        $responseData = $this->post(
            $url,
            $this->port,
            [
                'profil_id' => $profilId
            ],
            $this->defaultHeaders
        );

        return new TokenResponse($responseData);
    }

    /**
     * @param string $accessToken
     * @return LogoutResponse
     * @throws Exception
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
     * @param int $profilId
     * @return TokenResponse
     * @throws Exception
     */
    public function passwordlessRegistration(int $profilId): TokenResponse
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/registration/passwordless');

        $responseData = $this->post(
            $url,
            $this->port,
            [
                'profil_id' => $profilId
            ],
            $this->defaultHeaders
        );

        return new TokenResponse($responseData);
    }

    /**
     * @param string $refreshToken
     * @return TokenResponse
     * @throws Exception
     */
    public function refreshToken(
        string $refreshToken
    ): TokenResponse
    {
        $url = sprintf('%s%s', $this->baseUrl, '/piano/token/refresh');

        $responseData = $this->post(
            $url,
            $this->port,
            [
                'refresh_token' => $refreshToken,
            ],
            $this->defaultHeaders
        );

        return new TokenResponse($responseData);
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
                return [];
            }

            /**
             * If any unforeseen error occur or response code >= 400, return empty body
             * Error handling will need to be improved if we need to have a different business logic by error code or message
             */
            if (
                curl_errno($curl) ||
                curl_getinfo($curl, CURLINFO_HTTP_CODE) >= 400
            ) {
                return [];
            }

            return json_decode($response, true) ?? [];
        } finally {
            curl_close($curl);
        }
    }
}