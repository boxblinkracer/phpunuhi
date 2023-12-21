<?php

namespace PHPUnuhi\Services\Writers\Xml;

interface XmlWriterInterface
{

    /**
     * @param string $filename
     * @param string $content
     * @return void
     */
    public function saveXml(string $filename, string $content): void;

}