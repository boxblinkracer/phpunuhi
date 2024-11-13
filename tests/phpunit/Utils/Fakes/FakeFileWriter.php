<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Services\Writers\File\FileWriterInterface;

class FakeFileWriter implements FileWriterInterface
{
    private string $providedContent = '';

    private string $providedFilename = '';


    public function getProvidedContent(): string
    {
        return $this->providedContent;
    }

    public function getProvidedFilename(): string
    {
        return $this->providedFilename;
    }

    public function writeFile(string $filename, string $content): void
    {
        $this->providedFilename = $filename;
        $this->providedContent = $content;
    }
}
