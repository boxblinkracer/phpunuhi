<?php

namespace PHPUnuhi\Bundles\Translator\DeepL;

use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;

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
     * @throws \Exception
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = (string)$options['deepl-key'];
        $this->formality = (bool)$options['deepl-formal'];

        if (empty($this->apiKey)) {
            throw new \Exception('Please provide your API key for DeepL');
        }
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     * @throws \DeepL\DeepLException
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $formalValue = ($this->formality) ? 'more' : 'less';

        $translator = new \DeepL\Translator($this->apiKey);

        if ($targetLocale === 'en') {
            $targetLocale = 'en-GB';
        }

        $result = $translator->translateText(
            $text,
            null,
            $targetLocale,
            [
                'formality' => $formalValue,
            ]
        );

        if (is_array($result)) {
            return $result[0]->text;
        }

        return $result->text;
    }

}