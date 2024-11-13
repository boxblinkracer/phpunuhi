<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use Exception;
use PHPUnuhi\Services\Loaders\Xml\XmlLoaderInterface;
use SimpleXMLElement;

class FakeXmlLoader implements XmlLoaderInterface
{
    private string $xmlString;



    public function __construct(string $xmlString)
    {
        $this->xmlString = $xmlString;
    }


    public function loadXML(string $filename): SimpleXMLElement
    {
        $xml = simplexml_load_string($this->xmlString);

        if (!$xml) {
            throw new Exception('Invalid XML');
        }

        return $xml;
    }
}
