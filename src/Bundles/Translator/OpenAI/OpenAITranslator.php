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
    private const DEFAULT_MODEL = 'gpt-4-turbo';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $model;


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
            new CommandOption('openai-model', true),
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
        $this->model = isset($options['openai-model']) ? (string)$options['openai-model'] : '';

        $this->apiKey = trim($this->apiKey);
        $this->model = trim($this->model);

        if ($this->apiKey === '') {
            throw new Exception('OpenAI API Key must not be empty. Please provide a key');
        }

        if ($this->model === '') {
            $this->model = self::DEFAULT_MODEL;
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

        $prompt = "Translate this into " . $languageName . " and do ONLY return the translation: " . $text;

        $params = [
            'model' => $this->model,
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

        if (!isset($choices[0]['message'])) {
            return '';
        }

        if (!isset($choices[0]['message']['content'])) {
            return '';
        }

        return trim((string)$choices[0]['message']['content']);
    }
}
