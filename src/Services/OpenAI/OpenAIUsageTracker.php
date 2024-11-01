<?php

namespace PHPUnuhi\Services\OpenAI;

class OpenAIUsageTracker
{

    /**
     * @var OpenAIUsageTracker
     */
    private static $instance;

    /**
     * @var array<mixed>
     */
    private $requests = [];


    /**
     * @return OpenAIUsageTracker
     */
    public static function getInstance(): OpenAIUsageTracker
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param string $prompt
     * @param float $costUSD
     * @return void
     */
    public function addRequest(string $prompt, float $costUSD): void
    {
        $this->requests[] = [
            'prompt' => $prompt,
            'costUSD' => $costUSD,
        ];
    }

    public function getRequestCount() : int
    {
        return count($this->requests);
    }
    /**
     * Gets the total costs rounded to 2 decimal places
     * @return float
     */
    public function getTotalCostsUSD(): float
    {
        $totalCostsUSD = 0;
        foreach ($this->requests as $cost) {
            $totalCostsUSD += $cost['costUSD'];
        }

        return $totalCostsUSD;
    }
}
