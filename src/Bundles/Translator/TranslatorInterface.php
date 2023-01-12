<?php

namespace PHPUnuhi\Bundles\Translator;

interface TranslatorInterface
{

    /**
     * Gets the key name that is used to
     * identify this service
     * @return string
     */
    function getName(): string;

    /**
     * Gets a list of available CLI options
     * whenever this service is used in a command
     * @return CommandOption[]
     */
    function getOptions(): array;

    /**
     * Sets the CLI options for this service.
     * Please assign all API keys and other configurations in here.
     * @param array<mixed> $options
     * @return mixed
     */
    function setOptionValues(array $options);

    /**
     * Translates the given text from the source locale into the target locale.
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     */
    function translate(string $text, string $sourceLocale, string $targetLocale): string;

}
