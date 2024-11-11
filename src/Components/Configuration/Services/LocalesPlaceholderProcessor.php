<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Traits\StringTrait;

class LocalesPlaceholderProcessor
{
    use StringTrait;


    /**
     * Builds a full path that can be used to read the file.
     * Some files are relative in the configuration file and therefore their full path
     * would be the configuration file's directory + the relative path.
     * TODO this is just placed in here for now to avoid duplicate code, although it has nothing to do with locales
     * @param string $filename
     * @param string $configFilename
     * @return string
     */
    public function buildFullPath(string $filename, string $configFilename): string
    {
        $isAlreadyAbsoluteLocaleFile = $this->stringDoesStartsWith($filename, '/');

        if ($isAlreadyAbsoluteLocaleFile) {
            $configuredFileName = $filename;
        } elseif ($configFilename !== '') {
            $configuredFileName = dirname($configFilename) . '/' . $filename;
        } else {
            $configuredFileName = $filename;
        }

        return $configuredFileName;
    }

    /**
     * @param string $localeName
     * @param string $localeFile
     * @param string $basePath
     * @param string $configFilename
     * @return string
     */
    public function buildRealLocaleFilename(string $localeName, string $localeFile, string $basePath, string $configFilename): string
    {
        if ($localeFile === '') {
            return '';
        }

        # if we have a basePath, we also need to replace any values
        if (trim($basePath) !== '') {
            $localeFile = str_replace('%base_path%', $basePath, $localeFile);
        }

        $configuredFileName = $this->buildFullPath($localeFile, $configFilename);


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
