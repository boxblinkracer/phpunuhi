<?php

namespace PHPUnuhi\Services\OpenAI;

class OpenAIResponse
{

    /**
     * @var string
     */
    private $response;

    /**
     * @var float
     */
    private $costsUSD;

    /**
     * @param string $response
     * @param float $costsUSD
     */
    public function __construct(string $response, float $costsUSD)
    {
        $this->response = $response;
        $this->costsUSD = $costsUSD;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getCostsUSD(): float
    {
        return $this->costsUSD;
    }
}
