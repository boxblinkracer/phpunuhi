<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Translator\OpenAI;

use Exception;
use Locale;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\OpenAI\OpenAIClient;
use PHPUnuhi\Services\Placeholder\Placeholder;

class OpenAITranslator implements TranslatorInterface
{
    private const DEFAULT_MODEL = 'gpt-4-turbo';

    private string $apiKey = '';

    private string $model = '';


    public function getName(): string
    {
        return 'openai';
    }


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
     * @param Placeholder[] $foundPlaceholders
     * @throws Exception
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $languageName = Locale::getDisplayLanguage($targetLocale);

        $prompt = "Translate this into " . $languageName . " and do ONLY return the translation: " . $text;

        $client = new OpenAIClient($this->apiKey);

        $result = $client->chat($prompt, $this->model);

        return $result->getResponse();
    }
}
