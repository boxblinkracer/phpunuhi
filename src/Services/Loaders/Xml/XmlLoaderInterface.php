<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Loaders\Xml;

use SimpleXMLElement;

interface XmlLoaderInterface
{
    public function loadXML(string $filename): SimpleXMLElement;
}
