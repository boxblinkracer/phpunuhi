<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\OpenAI;

class OpenAIResponse
{
    private string $response;

    private float $costsUSD;


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
