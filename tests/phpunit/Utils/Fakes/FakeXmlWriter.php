<?php

namespace phpunit\Utils\Fakes;

use PHPUnuhi\Services\Writers\Xml\XmlWriterInterface;

class FakeXmlWriter implements XmlWriterInterface
{
    /**
     * @var string
     */
    private $providedXml;

    /**
     * @var string
     */
    private $providedFilename;


    /**
     * @return string
     */
    public function getProvidedXml(): string
    {
        return $this->providedXml;
    }

    /**
     * @return string
     */
    public function getProvidedFilename(): string
    {
        return $this->providedFilename;
    }


    /**
     * @param string $filename
     * @param string $content
     * @return void
     */
    public function saveXml(string $filename, string $content): void
    {
        $this->providedFilename = $filename;
        $this->providedXml = $content;
    }
}
