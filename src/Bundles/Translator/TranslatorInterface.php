<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Translator;

use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;

interface TranslatorInterface
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
     * Translates the given text from the source locale into the target locale.
     * @param Placeholder[] $foundPlaceholders
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string;
}
