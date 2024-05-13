<?php

namespace PHPUnuhi\Bundles\Translator\DeepL;

use DeepL\TextResult;
use DeepL\Translator;
use Exception;
use PHPUnuhi\Bundles\Translator\DeepL\Services\SupportedLanguages;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class DeeplTranslator implements TranslatorInterface
{

    /**
     *
     */
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

    /**
     * @var bool
     */
    private $formality;


    /**
     * @var PlaceholderEncoder
     */
    private $placeholderEncoder;


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'deepl';
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
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
     * @return void
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = isset($options['deepl-key']) ? (string)$options['deepl-key'] : '';
        $this->formality = isset($options['deepl-formal']) && (bool)$options['deepl-formal'];

        $this->apiKey = trim($this->apiKey);

        if ($this->apiKey === '') {
            throw new Exception('Please provide your API key for DeepL');
        }

        $this->placeholderEncoder = new PlaceholderEncoder();
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
        $text = $this->placeholderEncoder->encode($text, $foundPlaceholders);

        $formalValue = ($this->formality) ? 'more' : 'less';

        $translator = new Translator($this->apiKey);

        $supportedLanguages = new SupportedLanguages($translator);

        $targetLocale = $supportedLanguages->getAvailableLocale($targetLocale);

        $options = [

        ];

        if (in_array($targetLocale, self::ALLOWED_FORMALITY)) {
            $options['formality'] = $formalValue;
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
