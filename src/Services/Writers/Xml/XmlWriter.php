<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\Xml;

use DOMDocument;

class XmlWriter implements XmlWriterInterface
{
    public function saveXml(string $filename, string $content): void
    {
        if (is_dir($filename)) {
            throw new \Exception('Provided filename for the XML file is a directory: ' . $filename . '. Please provide a valid filename.');
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($content);
        $out = $dom->saveXML();

        file_put_contents($filename, $out);
    }
}
