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
        // TODO: Implement getOptions() method.
    }

    public function setOptionValues(array $options): void
    {
        // TODO: Implement setOptionValues() method.
    }

    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        // TODO: Implement translate() method.
    }
}
