<?php

namespace LeDevoir\PianoAuthSDK\Responses;

class TokenResponse
{
    /**
     * @var array
     */
    private $jsonData;

    public function __construct(array $jsonData)
    {
        $this->jsonData = $jsonData;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->jsonData['data']['access_token'] ?? '';
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->jsonData['data']['refresh_token'] ?? '';
    }
}