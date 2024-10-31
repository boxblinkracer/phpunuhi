<?php

namespace PHPUnuhi\Bundles\Spelling;

use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Text\Text;

interface SpellCheckerInterface
{

    /**
     * Gets the key name that is used to
     * identify this service
     * @return string
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
     * @return void
     */
    public function setOptionValues(array $options) : void;

    /**
     * @return string[]
     */
    public function getAvailableDictionaries(): array;

    /**
     * @param Text $text
     * @param string $locale the locale key of the text such as de-de, en-GB, de, etc.
     * @return SpellingValidationResult
     */
    public function validate(Text $text, string $locale): SpellingValidationResult;

    /**
     * @param Text $text
     * @param string $locale the locale key of the text such as de-de, en-GB, de, etc.
     * @return string
     */
    public function fixSpelling(Text $text, string $locale): string;
}
