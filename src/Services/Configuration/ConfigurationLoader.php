<?php

namespace PHPUnuhi\Services\Configuration;

use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\TranslationSuite;
use SimpleXMLElement;

class ConfigurationLoader
{

    /**
     * @param string $configFilename
     * @return Configuration
     * @throws \Exception
     */
    public function load(string $configFilename)
    {
        $xmlString = (string)file_get_contents($configFilename);
        $xmlSettings = simplexml_load_string($xmlString);

        if (!$xmlSettings instanceof SimpleXMLElement) {
            throw new \Exception('Error when loading configuration. Invalid XML: ' . $configFilename);
        }


        $suites = [];

        /** @var SimpleXMLElement $translation */
        foreach ($xmlSettings->translations->children() as $translation) {

            $name = (string)$translation['name'];

            $files = [];

            /** @var SimpleXMLElement $childNode */
            foreach ($translation->children() as $childNode) {

                $nodeType = $childNode->getName();
                $nodeValue = (string)$childNode[0];

                switch ($nodeType) {
                    case 'file':
                        $file = $nodeValue;
                        $files[] = realpath(dirname($configFilename) . '/' . $file);
                        break;
                }
            }

            $suites[] = new TranslationSuite(
                $name,
                $files
            );
        }

        return new Configuration($suites);
    }
}