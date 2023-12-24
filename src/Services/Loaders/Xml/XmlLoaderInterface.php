<?php

namespace PHPUnuhi\Services\Loaders\Xml;

use SimpleXMLElement;

interface XmlLoaderInterface
{

    /**
     * @param string $filename
     * @return SimpleXMLElement
     */
    public function loadXML(string $filename): SimpleXMLElement;
}
