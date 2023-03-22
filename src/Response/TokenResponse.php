<?php

namespace LeDevoir\PianoAuthSDK\Response;

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
    public function getToken(): string
    {
        return $this->jsonData['data']['piano_token'] ?? '';
    }
}