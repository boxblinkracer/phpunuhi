<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Traits;

use SimpleXMLElement;

trait XmlLoaderTrait
{
    protected function loadXml(string $xml): SimpleXMLElement
    {
        $element = simplexml_load_string($xml);

        if ($element === false) {
            return new SimpleXMLElement('');
        }

        return $element;
    }
}
