<?php

namespace PHPUnuhi\Bundles\Translation\OpenAI;

use Locale;
use Orhanerday\OpenAi\OpenAi;
use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class OpenAITranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     * @throws \Exception
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $languageName = Locale::getDisplayLanguage($targetLocale);

        $prompt = "Translate this into " . $languageName . ":" . $text;

        $params = [
            'model' => "text-davinci-003",
            'prompt' => $prompt,
            'temperature' => 0,
            'max_tokens' => 100,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
        ];


        $openAI = new OpenAi($this->apiKey);

        $complete = $openAI->completion($params);

        $json = json_decode($complete, true);

        if (!is_array($json)) {
            return '';
        }

        if (!isset($json['choices'])) {
            return '';
        }

        $choices = $json['choices'];

        if (!is_array($choices) || count($choices) <= 0) {
            return '';
        }

        if (!isset($choices[0]['text'])) {
            return '';
        }

        return trim((string)$choices[0]['text']);
    }

}
