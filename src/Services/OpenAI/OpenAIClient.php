<?php

namespace PHPUnuhi\Services\OpenAI;

use Exception;
use Orhanerday\OpenAi\OpenAi;

class OpenAIClient
{
    /**
     * @var string
     */
    private $apiKey;

    private const DEFAULT_TOKEN_PRICE = 0.00002;


    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @param array<mixed> $params
     * @throws Exception
     * @return OpenAIResponse
     */
    public function chat(array $params): OpenAIResponse
    {
        $openAI = new OpenAi($this->apiKey);

        $complete = (string)$openAI->chat($params);

        $json = json_decode($complete, true);

        if (!is_array($json)) {
            throw new Exception('OpenAI Error: Invalid response');
        }


        $costUSD = 0;
        if (isset($json['usage'])) {
            $totalTokens = $json['usage']['total_tokens'];

            $pricingData = json_decode((string)file_get_contents(__DIR__ . '/pricing.json'), true);
            $model = $params['model'] ?? 'gpt-3.5-turbo';
            $costPerTokenUSD = $pricingData[$model] ?? self::DEFAULT_TOKEN_PRICE;

            $costUSD = $totalTokens * $costPerTokenUSD;
        }

        if (isset($json['error'])) {
            $msg = 'OpenAI Error: ' . $json['error']['message'];
            throw new Exception($msg);
        }

        if (!isset($json['choices'])) {
            return new OpenAIResponse('', $costUSD);
        }

        $choices = $json['choices'];

        if (!is_array($choices) || count($choices) <= 0) {
            return new OpenAIResponse('', $costUSD);
        }

        if (!isset($choices[0]['message'])) {
            return new OpenAIResponse('', $costUSD);
        }

        if (!isset($choices[0]['message']['content'])) {
            return new OpenAIResponse('', $costUSD);
        }

        $text = trim((string)$choices[0]['message']['content']);

        return new OpenAIResponse($text, $costUSD);
    }
}
