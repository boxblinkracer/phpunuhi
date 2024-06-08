<?php

namespace PHPUnuhi\Tests\Utils\Fakes;

use Exception;
use PHPUnuhi\Services\Loaders\Xml\XmlLoaderInterface;
use SimpleXMLElement;

class FakeXmlLoader implements XmlLoaderInterface
{

    /**
     * @var string
     */
    private $xmlString;


    /**
     * @param string $xmlString
     */
    public function __construct(string $xmlString)
    {
        $this->xmlString = $xmlString;
    }

    /**
     * @param string $filename
     * @return SimpleXMLElement
     */
    public function loadXML(string $filename): SimpleXMLElement
    {
        $xml = simplexml_load_string($this->xmlString);

        if (!$xml) {
            throw new Exception('Invalid XML');
        }

        return $xml;
    }
}
