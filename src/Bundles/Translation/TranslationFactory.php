<?php

namespace PHPUnuhi\Bundles\Translation;

use PHPUnuhi\Bundles\Translation\JSON\JSONTranslationLoader;
use PHPUnuhi\Bundles\Translation\JSON\JSONTranslationSaver;
use PHPUnuhi\Bundles\Translation\JSON\JSONTranslationValidator;

class TranslationFactory
{


    /**
     * @param string $format
     * @return TranslationLoaderInterface
     * @throws \Exception
     */
    public static function getLoaderFromFormat(string $format): TranslationLoaderInterface
    {
        switch (strtolower($format)) {
            case Format::JSON:
                return new JSONTranslationLoader();

            default:
                throw new \Exception('No translation loader found for format: ' . $format);
        }
    }

    /**
     * @param string $format
     * @param int $jsonIntent
     * @param bool $jsonSort
     * @return TranslationSaverInterface
     * @throws \Exception
     */
    public static function getSaverFromFormat(string $format, int $jsonIntent, bool $jsonSort): TranslationSaverInterface
    {
        switch (strtolower($format)) {
            case Format::JSON:
                return new JSONTranslationSaver($jsonIntent, $jsonSort);

            default:
                throw new \Exception('No translation saver found for format: ' . $format);
        }
    }

    /**
     * @param string $format
     * @return ValidationInterface
     * @throws \Exception
     */
    public static function getValidatorFromFormat(string $format): ValidationInterface
    {
        switch (strtolower($format)) {
            case Format::JSON:
                return new JSONTranslationValidator();

            default:
                throw new \Exception('No translation validator found for format: ' . $format);
        }
    }
}
