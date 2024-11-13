<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\Xml;

use DOMDocument;

class XmlWriter implements XmlWriterInterface
{
    public function saveXml(string $filename, string $content): void
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($content);
        $out = $dom->saveXML();

        file_put_contents($filename, $out);
    }
}
