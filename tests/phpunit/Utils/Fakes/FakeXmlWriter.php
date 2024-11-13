<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Services\Writers\Xml\XmlWriterInterface;

class FakeXmlWriter implements XmlWriterInterface
{
    private string $providedXml = '';

    private string $providedFilename = '';


    public function getProvidedXml(): string
    {
        return $this->providedXml;
    }

    public function getProvidedFilename(): string
    {
        return $this->providedFilename;
    }

    public function saveXml(string $filename, string $content): void
    {
        $this->providedFilename = $filename;
        $this->providedXml = $content;
    }
}
