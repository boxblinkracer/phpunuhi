<?php

namespace PHPUnuhi\Bundles\Translator\DeepL;

use DeepL\Translator;
use Exception;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class DeeplTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $formality;

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
     * @return void
     * @throws Exception
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = (string)$options['deepl-key'];
        $this->formality = (bool)$options['deepl-formal'];

        if ($this->apiKey === '' || $this->apiKey === '0') {
            throw new Exception('Please provide your API key for DeepL');
        }

        $this->placeholderEncoder = new PlaceholderEncoder();
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param Placeholder[] $foundPlaceholders
     * @return string
     * @throws Exception
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $text = $this->placeholderEncoder->encode($text, $foundPlaceholders);

        $formalValue = ($this->formality) ? 'more' : 'less';

        $translator = new Translator($this->apiKey);

        if ($targetLocale === 'en') {
            $targetLocale = 'en-GB';
        }
        if ($targetLocale === 'de-DE') {
            $targetLocale = 'de';
        }


        $options = [

        ];

        if (in_array($targetLocale, self::ALLOWED_FORMALITY)) {
            $options['formality'] = $formalValue;
        }

        $result = $translator->translateText(
            $text,
            null,
            $targetLocale,
            $options
        );

        if (is_array($result)) {
            return $result[0]->text;
        }

        $result = $result->text;

        if ($foundPlaceholders !== []) {
            # decode our string so that we have the original placeholder values again (%productName%)
            return $this->placeholderEncoder->decode($result, $foundPlaceholders);
        }

        return $result;
    }

}