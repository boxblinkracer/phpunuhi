<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\OpenAI;

use Exception;
use Orhanerday\OpenAi\OpenAi;

class OpenAIClient
{
    private string $apiKey;



    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @throws Exception
     */
    public function chat(string $prompt, string $model): OpenAIResponse
    {
        $params = [
            'model' => $model,
            'temperature' => 0.3,
            'max_tokens' => 100,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
            'messages' => [
                [
                    "role" => "user",
                    "content" => $prompt
                ],
            ]
        ];

        $openAI = new OpenAi($this->apiKey);

        $complete = (string)$openAI->chat($params);

        $json = json_decode($complete, true);

        if (!is_array($json)) {
            throw new Exception('OpenAI Error: Invalid response');
        }


        $costUSD = 0;
        if (isset($json['usage'])) {
            $pricingData = json_decode((string)file_get_contents(__DIR__ . '/pricing.json'), true);

            if (isset($pricingData[$model])) {
                $costsInputToken = $pricingData[$model]['input'] ?? 0;
                $costsOutputToken = $pricingData[$model]['output'] ?? 0;

                $totalInputTokens = $json['usage']['prompt_tokens'];
                $totalOutputTokens = $json['usage']['completion_tokens'];

                $costUSD = ($totalInputTokens * $costsInputToken) + ($totalOutputTokens * $costsOutputToken);
            }
        }

        if (isset($json['error'])) {
            $msg = 'OpenAI Error: ' . $json['error']['message'];
            throw new Exception($msg);
        }

        # always use our singleton usage tracker
        OpenAIUsageTracker::getInstance()->addRequest($prompt, $costUSD);


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
