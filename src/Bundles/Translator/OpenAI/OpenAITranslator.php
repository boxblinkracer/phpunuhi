<?php

namespace PHPUnuhi\Bundles\Translator\OpenAI;

use Exception;
use Locale;
use Orhanerday\OpenAi\OpenAi;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;

class OpenAITranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'openai';
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
            new CommandOption('openai-key', true),
        ];
    }

    /**
     * @param array<mixed> $options
     * @throws Exception
     * @return void
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = isset($options['openai-key']) ? (string)$options['openai-key'] : '';

        $this->apiKey = trim($this->apiKey);

        if ($this->apiKey === '') {
            throw new Exception('OpenAI API Key must not be empty. Please provide a key');
        }
    }


    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param Placeholder[] $foundPlaceholders
     * @throws Exception
     * @return string
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $languageName = Locale::getDisplayLanguage($targetLocale);

        $prompt = "Translate this into " . $languageName . ": " . $text;


        $params = [
            'model' => "gpt-3.5-turbo-instruct",
            'prompt' => $prompt,
            'temperature' => 0.3,
            'max_tokens' => 100,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
        ];

        $openAI = new OpenAi($this->apiKey);

        $complete = (string)$openAI->completion($params);

        $json = json_decode($complete, true);

        if (!is_array($json)) {
            return '';
        }

        if (isset($json['error'])) {
            $msg = 'OpenAI Error: ' . $json['error']['message'];
            throw new Exception($msg);
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
