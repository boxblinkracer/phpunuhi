<?php

namespace PHPUnuhi\Services\Loaders\Xml;

use Exception;
use SimpleXMLElement;

class XmlLoader implements XmlLoaderInterface
{

    /**
     * @param string $filename
     * @throws Exception
     * @return SimpleXMLElement
     */
    public function loadXML(string $filename): SimpleXMLElement
    {
        if (!file_exists($filename)) {
            throw new Exception('Configuration file not found: ' . $filename);
        }

        $rootXmlString = (string)file_get_contents($filename);

        $xml = simplexml_load_string($rootXmlString);

        if (!$xml instanceof SimpleXMLElement) {
            throw new Exception('Could not parse XML file: ' . $filename);
        }

        return $xml;
    }
}
