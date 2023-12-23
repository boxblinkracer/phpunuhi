<?php

namespace phpunit\Utils\Traits;

use SimpleXMLElement;

trait XmlLoaderTrait
{

    /**
     * @param string $xml
     * @return SimpleXMLElement
     */
    protected function loadXml(string $xml): SimpleXMLElement
    {
        $element = simplexml_load_string($xml);

        if ($element === false) {
            return new SimpleXMLElement('');
        }

        return $element;
    }
}
