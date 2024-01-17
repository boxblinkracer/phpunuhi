<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;

class ConfigurationValidator
{

    /**
     * @param Configuration $configuration
     * @throws ConfigurationException
     * @return void
     */
    public function validateConfig(Configuration $configuration): void
    {
        $foundSets = [];

        if ($configuration->getTranslationSets() === []) {
            throw new ConfigurationException('No TranslationSets found');
        }

        foreach ($configuration->getTranslationSets() as $set) {
            if ($set->getName() === '') {
                throw new ConfigurationException('TranslationSet has no name. This is required!');
            }

            if ($set->getFormat() === '') {
                throw new ConfigurationException('TranslationSet has no format. This is required!');
            }

            if (in_array($set->getName(), $foundSets)) {
                throw new ConfigurationException('TranslationSet "' . $set->getName() . '" has already been found');
            }

            $foundSets[] = $set->getName();


            $foundLocales = [];

            foreach ($set->getLocales() as $locale) {
                if ($locale->getName() === '') {
                    throw new ConfigurationException('Locale has no name. This is required!');
                }

                if (in_array($locale->getName(), $foundLocales)) {
                    throw new ConfigurationException('Locale "' . $locale->getName() . '" has already been found in Translation-Set: ' . $set->getName());
                }

                $filename = $locale->getFilename();
                if ($filename !== '' && !file_exists($filename)) {
                    throw new ConfigurationException('Attention, translation file not found: ' . $filename);
                }

                $foundLocales[] = $locale->getName();
            }
        }
    }
}
