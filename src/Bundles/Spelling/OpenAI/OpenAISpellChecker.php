<?php

namespace PHPUnuhi\Bundles\Spelling\OpenAI;

use Locale;
use Orhanerday\OpenAi\OpenAi;
use PHPUnuhi\Bundles\Spelling\Exception\SpellingValidationNotSupportedException;
use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;


class OpenAISpellChecker implements SpellCheckerInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @param string $apiKey
     * @throws \Exception
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;

        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API Key must not be empty. Please provide a key');
        }
    }

    /**
     * @param string $text
     * @param string $locale
     * @return bool
     * @throws SpellingValidationNotSupportedException
     */
    public function validateSpelling(string $text, string $locale): bool
    {
        throw new SpellingValidationNotSupportedException();
    }

    /**
     * @param string $text
     * @param string $locale
     * @return string
     * @throws \Exception
     */
    public function fixSpelling(string $text, string $locale): string
    {
        $language = $this->getLanguage($locale);

        $prompt = 'Correct misspelling of this ' . $language . ' text with hunspell: ' . $text;
        $model = "text-davinci-003";

        $params = [
            'model' => $model,
            'prompt' => $prompt,
            'temperature' => 0.7,
            'max_tokens' => 256,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ];

        return $this->sendRequest($params);
    }

    /**
     * @param string $locale
     * @return string
     */
    private function getLanguage(string $locale): string
    {
        return Locale::getDisplayLanguage($locale);
    }

    /**
     * @param array<mixed> $params
     * @return string
     * @throws \Exception
     */
    private function sendRequest(array $params): string
    {
        $openAI = new OpenAi($this->apiKey);

        $complete = (string)$openAI->completion($params);

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
