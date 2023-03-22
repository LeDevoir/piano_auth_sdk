<?php

namespace LeDevoir\PianoAuthSDK\Response;

class LogoutResponse
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
     * Jti confirms logout has been successful
     *
     * @return string
     */
    public function getJti(): string
    {
        return $this->jsonData['data']['jti'] ?? '';
    }
}