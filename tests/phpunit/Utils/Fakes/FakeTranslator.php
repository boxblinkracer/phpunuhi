<?php

namespace phpunit\Utils\Fakes;

use PHPUnuhi\Bundles\Translator\TranslatorInterface;

class FakeTranslator implements TranslatorInterface
{
    public function getName(): string
    {
        return 'fake';
    }

    public function getOptions(): array
    {
        return [];
    }

    public function setOptionValues(array $options): void
    {
    }

    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        return $text;
    }
}
