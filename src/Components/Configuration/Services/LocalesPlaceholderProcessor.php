<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Traits\StringTrait;

class LocalesPlaceholderProcessor
{
    use StringTrait;


    /**
     * @param string $localeName
     * @param string $xmlFilename
     * @param string $basePath
     * @param string $configFilename
     * @return string
     */
    public function buildRealFilename(string $localeName, string $xmlFilename, string $basePath, string $configFilename): string
    {
        if ($xmlFilename === '') {
            return '';
        }

        # if we have a basePath, we also need to replace any values
        if (trim($basePath) !== '') {
            $xmlFilename = str_replace('%base_path%', $basePath, $xmlFilename);
        }

        $isAbsoluteFilename = $this->stringDoesStartsWith($xmlFilename, '/');

        # if we have a relative config filename and a given config directory
        # then we build the absolute path from the directory of the given configuration file working directory
        if (!$isAbsoluteFilename && $configFilename !== '') {
            $configuredFileName = dirname($configFilename) . '/' . $xmlFilename;
        } else {
            $configuredFileName = $xmlFilename;
        }

        $filename = file_exists($configuredFileName) ? (string)realpath($configuredFileName) : $configuredFileName;

        # replace our locale-name placeholders
        $filename = str_replace('%locale%', $localeName, $filename);
        $filename = str_replace('%locale_uc%', strtoupper($localeName), $filename);
        $filename = str_replace('%locale_lc%', strtolower($localeName), $filename);


        # clear duplicate slashes that exist somehow
        $filename = str_replace('//', '/', $filename);
        # replace duplicate occurrences of ./
        # in the front it can be removed and in between it's just an ugly path
        $filename = str_replace('./', '', $filename);

        return (string)$filename;
    }
}
