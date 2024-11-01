<?php

namespace PHPUnuhi\Bundles\Spelling\OpenAI;

use Exception;
use Locale;
use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Text\Text;
use PHPUnuhi\Services\OpenAI\OpenAIClient;
use RuntimeException;

class OpenAISpellChecker implements SpellCheckerInterface
{
    private const DEFAULT_MODEL = 'gpt-4-turbo';

    private const SPELLING_RULES = 'Only correct spelling errors, grammar, leave placeholders or wildcards intact, and ignore words in other languages. Do not translate or suggest translations.';

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
            throw new RuntimeException('OpenAI API Key must not be empty. Please provide a key');
        }

        if ($this->model === '') {
            $this->model = self::DEFAULT_MODEL;
        }
    }


    /**
     * @return string[]
     */
    public function getAvailableDictionaries(): array
    {
        return [
            'everything ;)'
        ];
    }

    /**
     * @param Text $text
     * @param string $locale
     * @throws Exception
     * @return SpellingValidationResult
     */
    public function validate(Text $text, string $locale): SpellingValidationResult
    {
        $language = Locale::getDisplayLanguage($locale);

        $prompt = 'Check spelling of this ' . $language . ' text using Hunspell. Do only return your recommended text, nothing more. ' . self::SPELLING_RULES . ': ' . $text->getEncodedText();

        $recommendedTextResult = (new OpenAIClient($this->apiKey))->chat($prompt, $this->model);

        $isSpellingValid = $recommendedTextResult->getResponse() === $text->getEncodedText();

        return new SpellingValidationResult($isSpellingValid, $locale, $recommendedTextResult->getResponse(), []);
    }

    /**
     * @param Text $text
     * @param string $locale
     * @throws \Exception
     * @return string
     */
    public function fixSpelling(Text $text, string $locale): string
    {
        $language = Locale::getDisplayLanguage($locale);

        $prompt = 'Check spelling of this ' . $language . ' text using Hunspell. Do only return your fixed text, nothing more. ' . self::SPELLING_RULES . ': ' . $text->getEncodedText();

        $result = (new OpenAIClient($this->apiKey))->chat($prompt, $this->model);

        return $result->getResponse();
    }
}
