<?php

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Services\Writers\File\FileWriterInterface;

class FakeFileWriter implements FileWriterInterface
{

    /**
     * @var string
     */
    private $providedContent;

    /**
     * @var string
     */
    private $providedFilename;


    /**
     * @return string
     */
    public function getProvidedContent(): string
    {
        return $this->providedContent;
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
    public function writeFile(string $filename, string $content): void
    {
        $this->providedFilename = $filename;
        $this->providedContent = $content;
    }
}
