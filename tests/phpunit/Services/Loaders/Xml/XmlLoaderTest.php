<?php

namespace phpunit\Services\Loaders\Xml;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use SimpleXMLElement;

class XmlLoaderTest extends TestCase
{
    /**
     * @var XmlLoader
     */
    private $loader;

    /**
     * @var string
     */
    private $invalidXmlFile;

    /**
     * @var string
     */
    private $validXmlFile;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->loader = new XmlLoader();

        $this->invalidXmlFile = __DIR__ . '/invalid_file.xml';
        file_put_contents($this->invalidXmlFile, 'Invalid XML content');

        $validXmlContent = '<?xml version="1.0" encoding="UTF-8"?><root><node>Value</node></root>';
        $this->validXmlFile = __DIR__ . '/valid_file.xml';
        file_put_contents($this->validXmlFile, $validXmlContent);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        if (file_exists($this->invalidXmlFile)) {
            unlink($this->invalidXmlFile);
        }

        if (file_exists($this->validXmlFile)) {
            unlink($this->validXmlFile);
        }
    }

    /**
     * @return void
     */
    public function testLoadXMLFileNotFound(): void
    {
        $nonExistentFile = 'path/to/non_existent_file.xml';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Configuration file not found: ' . $nonExistentFile);

        $this->loader->loadXML($nonExistentFile);
    }

    /**
     * @return void
     */
    public function testLoadXMLInvalidFile(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Could not parse XML file: ' . $this->invalidXmlFile);

        $this->loader->loadXML($this->invalidXmlFile);
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testLoadXMLValidFile(): void
    {
        $result = $this->loader->loadXML($this->validXmlFile);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);
    }
}
