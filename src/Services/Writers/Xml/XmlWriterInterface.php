<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\Xml;

interface XmlWriterInterface
{
    public function saveXml(string $filename, string $content): void;
}
