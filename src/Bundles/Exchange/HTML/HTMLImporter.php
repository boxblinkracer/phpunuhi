<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;
use SplFileObject;

class HTMLImporter
{

    use StringTrait;

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return void
     */
    public function import(TranslationSet $set, string $filename): void
    {
        $foundData = [];

        foreach (new SplFileObject($filename) as $line) {

            if ($line === false) {
                $line = '';
            }

            $line = str_replace(PHP_EOL, '', $line);

            if (is_array($line)) {
                $line = '';
            }

            if (trim($line) === '') {
                continue;
            }

            $fullKeyWithLocale = explode('=', $line)[0];

            $key = '';
            $group = '';
            $localeID = '';

            if ($this->stringStartsWith($fullKeyWithLocale, 'group--')) {

                $group = explode('.', $fullKeyWithLocale)[0];
                $group = str_replace('group--', '', $group);

                $key = str_replace('group--' . $group . '.', '', $fullKeyWithLocale);
            }

            if ($this->stringContains($key, '--')) {
                $localeID = explode('--', $key)[1];
                $key = explode('--', $key)[0];
            }

            $value = explode('=', $line)[1];


            $foundData[] = [
                'key' => $key,
                'group' => $group,
                'locale' => $localeID,
                'value' => $value,
            ];
        }


        foreach ($set->getLocales() as $locale) {

            $newTranslations = [];

            foreach ($foundData as $data) {
                if ($data['locale'] === $locale->getExchangeIdentifier()) {
                    $newTranslations[] = new Translation($data['key'], $data['value'], $data['group']);
                }
            }

            $locale->setTranslations($newTranslations);
        }
    }


}