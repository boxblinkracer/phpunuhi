<?php

namespace PHPUnuhi\Services\Writers\Xml;

use DOMDocument;

class XmlWriter implements XmlWriterInterface
{

    /**
     * @param string $filename
     * @param string $content
     * @return void
     */
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
