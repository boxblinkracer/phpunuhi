<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Spelling;

use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Text\Text;

interface SpellCheckerInterface
{
    /**
     * Gets the key name that is used to
     * identify this service
     */
    public function getName(): string;

    /**
     * Gets a list of available CLI options
     * whenever this service is used in a command
     * @return CommandOption[]
     */
    public function getOptions(): array;

    /**
     * Sets the CLI options for this service.
     * Please assign all API keys and other configurations in here.
     * @param array<mixed> $options
     */
    public function setOptionValues(array $options): void;

    /**
     * @return string[]
     */
    public function getAvailableDictionaries(): array;

    /**
     * @param string $locale the locale key of the text such as de-de, en-GB, de, etc.
     */
    public function validate(Text $text, string $locale): SpellingValidationResult;

    /**
     * @param string $locale the locale key of the text such as de-de, en-GB, de, etc.
     */
    public function fixSpelling(Text $text, string $locale): string;
}
