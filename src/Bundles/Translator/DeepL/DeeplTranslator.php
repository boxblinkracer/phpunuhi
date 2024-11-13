<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Translator\DeepL;

use DeepL\TextResult;
use DeepL\TranslateTextOptions as DeeplOptions;
use DeepL\Translator;
use Exception;
use PHPUnuhi\Bundles\Translator\DeepL\Services\SupportedLanguages;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class DeeplTranslator implements TranslatorInterface
{
    public const ENV_DEEPL_KEY = 'DEEPL_KEY';

    public const ENV_DEEPL_FORMAL = 'DEEPL_FORMAL';


    public const ALLOWED_FORMALITY = [
        'de',
        'nl',
        'fr',
        'it',
        'pl',
        'ru',
        'es',
        'pt'
    ];

    /**
     * @var string
     */
    private $apiKey;

    private bool $formality = false;


    private PlaceholderEncoder $placeholderEncoder;


    public function __construct()
    {
        $this->placeholderEncoder = new PlaceholderEncoder();
    }


    public function getName(): string
    {
        return 'deepl';
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function isFormality(): bool
    {
        return $this->formality;
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
            new CommandOption('deepl-key', true),
            new CommandOption('deepl-formal', false),
        ];
    }

    /**
     * @param array<mixed> $options
     * @throws Exception
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = isset($options['deepl-key'])
            ? (string)$options['deepl-key']
            : (string)getenv(self::ENV_DEEPL_KEY);
        $this->formality = isset($options['deepl-formal'])
            ? (bool)$options['deepl-formal']
            : (bool)getenv(self::ENV_DEEPL_FORMAL);

        $this->apiKey = trim($this->apiKey);

        if ($this->apiKey === '') {
            throw new Exception('Please provide your API key for DeepL');
        }
    }

    /**
     * @param Placeholder[] $foundPlaceholders
     * @throws Exception
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $text = $this->placeholderEncoder->encode($text, $foundPlaceholders);

        $formalValue = ($this->formality) ? 'more' : 'less';

        $translator = new Translator($this->apiKey);

        $supportedLanguages = new SupportedLanguages($translator);

        $targetLocale = $supportedLanguages->getAvailableLocale($targetLocale);

        $options = [];

        if (in_array($targetLocale, self::ALLOWED_FORMALITY)) {
            $options[DeeplOptions::FORMALITY] = $formalValue;
        }

        if ($text !== strip_tags($text)) {
            $options[DeeplOptions::TAG_HANDLING] = 'html';
        }

        /** @var TextResult|TextResult[] $result */
        $result = $translator->translateText(
            $text,
            null,
            $targetLocale,
            $options
        );

        $result = $result instanceof TextResult ? $result->text : $result[0]->text;

        if ($foundPlaceholders !== []) {
            # decode our string so that we have the original placeholder values again (%productName%)
            return $this->placeholderEncoder->decode($result, $foundPlaceholders);
        }

        return $result;
    }
}
