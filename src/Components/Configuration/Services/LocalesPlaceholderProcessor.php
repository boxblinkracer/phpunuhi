<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Traits\StringTrait;

class LocalesPlaceholderProcessor
{
    use StringTrait;


    /**
     * @param string $localeName
     * @param string $localeFile
     * @param string $basePath
     * @param string $configFilename
     * @return string
     */
    public function buildRealFilename(string $localeName, string $localeFile, string $basePath, string $configFilename): string
    {
        if ($localeFile === '') {
            return '';
        }

        # if we have a basePath, we also need to replace any values
        if (trim($basePath) !== '') {
            $localeFile = str_replace('%base_path%', $basePath, $localeFile);
        }

        $isAlreadyAbsoluteLocaleFile = $this->stringDoesStartsWith($localeFile, '/');

        if ($isAlreadyAbsoluteLocaleFile) {
            $configuredFileName = $localeFile;
        } elseif ($configFilename !== '') {
            $configuredFileName = dirname($configFilename) . '/' . $localeFile;
        } else {
            $configuredFileName = $localeFile;
        }


        $filename = file_exists($configuredFileName) ? (string)realpath($configuredFileName) : $configuredFileName;


        # replace our locale-name placeholders
        $filename = str_replace('%locale%', $localeName, $filename);
        $filename = str_replace('%locale_uc%', strtoupper($localeName), $filename);
        $filename = str_replace('%locale_lc%', strtolower($localeName), $filename);
        $filename = str_replace('%locale_un%', str_replace('-', '_', $localeName), $filename);

        # clear duplicate slashes that exist somehow
        $filename = str_replace('//', '/', $filename);

        $filename = str_replace('../', '__DOUBLE__', $filename);

        # replace duplicate occurrences of ./
        # in the front it can be removed and in between it's just an ugly path
        $filename = str_replace('./', '', $filename);

        $filename = str_replace('__DOUBLE__', '../', $filename);

        return (string)$filename;
    }
}
