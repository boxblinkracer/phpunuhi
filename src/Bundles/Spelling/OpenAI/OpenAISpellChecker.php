<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Spelling\OpenAI;

use Exception;
use Locale;
use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Text\Text;
use PHPUnuhi\Services\OpenAI\OpenAIClient;
use PHPUnuhi\Traits\StringTrait;
use RuntimeException;

class OpenAISpellChecker implements SpellCheckerInterface
{
    use StringTrait;

    private const DEFAULT_MODEL = 'gpt-4-turbo';

    private const SPELLING_RULES = 'Only correct spelling errors, grammar. Keep placeholders and wildcards. Ignore words in other languages - just return the input text in that case. Do not translate or suggest translations.';

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
     * @throws Exception
     */
    public function validate(Text $text, string $locale): SpellingValidationResult
    {
        $language = Locale::getDisplayLanguage($locale);

        $prompt = 'Check spelling of this ' . $language . ' text using Hunspell. Do only return your recommended text, nothing more. ' . self::SPELLING_RULES . ': ' . $text->getEncodedText();

        $recommendedTextResult = (new OpenAIClient($this->apiKey))->chat($prompt, $this->model);

        $recommendedText = $recommendedTextResult->getResponse();

        $recommendedText = $this->cleanResult($text->getEncodedText(), $recommendedText);

        $isSpellingValid = $recommendedText === $text->getEncodedText();

        return new SpellingValidationResult($isSpellingValid, $locale, $recommendedText, []);
    }

    /**
     * @throws Exception
     */
    public function fixSpelling(Text $text, string $locale): string
    {
        $language = Locale::getDisplayLanguage($locale);

        $prompt = 'Check spelling of this ' . $language . ' text using Hunspell. Do only return your fixed text, nothing more. ' . self::SPELLING_RULES . ': ' . $text->getEncodedText();

        $result = (new OpenAIClient($this->apiKey))->chat($prompt, $this->model);

        $recommendedText = $result->getResponse();

        return $this->cleanResult($text->getEncodedText(), $recommendedText);
    }

    private function cleanResult(string $originalText, string $suggestedText): string
    {
        if (!$this->stringDoesEndsWith($originalText, '.') && $this->stringDoesEndsWith($suggestedText, '.')) {
            $suggestedText = rtrim($suggestedText, '.');
        }

        # we do not consider empty spaces (for now)
        # so lets just ignore this
        # TODO....maybe all these things could be done in some kind of global post-processor spell checker?!
        if (trim($originalText) === trim($suggestedText)) {
            return $originalText;
        }

        return $suggestedText;
    }
}
