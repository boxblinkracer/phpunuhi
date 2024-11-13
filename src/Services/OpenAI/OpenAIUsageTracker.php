<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\OpenAI;

class OpenAIUsageTracker
{
    private static ?\PHPUnuhi\Services\OpenAI\OpenAIUsageTracker $instance = null;

    /**
     * @var array<mixed>
     */
    private array $requests = [];



    public static function getInstance(): OpenAIUsageTracker
    {
        if (!self::$instance instanceof \PHPUnuhi\Services\OpenAI\OpenAIUsageTracker) {
            self::$instance = new self();
        }

        return self::$instance;
    }



    public function addRequest(string $prompt, float $costUSD): void
    {
        $this->requests[] = [
            'prompt' => $prompt,
            'costUSD' => $costUSD,
        ];
    }

    public function getRequestCount(): int
    {
        return count($this->requests);
    }
    /**
     * Gets the total costs rounded to 2 decimal places
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
