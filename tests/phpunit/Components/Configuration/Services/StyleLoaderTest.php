<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\XmlLoaderTrait;
use PHPUnuhi\Configuration\Services\StyleLoader;
use PHPUnuhi\Models\Configuration\CaseStyle;

class StyleLoaderTest extends TestCase
{
    use XmlLoaderTrait;


    /**
     * @return void
     */
    public function testLoadStyles(): void
    {
        $xmlString = <<<XML
<styles>
    <style level="1">camel</style>
    <style>pascal</style>
    <style level="2">kebab</style>
</styles>
XML;

        $xml = $this->loadXml($xmlString);

        $loader = new StyleLoader();
        $result = $loader->loadStyles($xml);


        $this->assertCount(3, $result);

        $this->assertInstanceOf(CaseStyle::class, $result[0]);
        $this->assertInstanceOf(CaseStyle::class, $result[1]);
        $this->assertInstanceOf(CaseStyle::class, $result[2]);

        $this->assertEquals('camel', $result[0]->getName());
        $this->assertEquals(1, $result[0]->getLevel());

        $this->assertEquals('pascal', $result[1]->getName());
        $this->assertEquals(-1, $result[1]->getLevel());

        $this->assertEquals('kebab', $result[2]->getName());
        $this->assertEquals(2, $result[2]->getLevel());
    }

    /**
     * @return void
     */
    public function testLoadWithoutStylesNode(): void
    {
        $xmlString = <<<XML
<root>
</root>
XML;

        $xml = $this->loadXml($xmlString);

        $loader = new StyleLoader();
        $result = $loader->loadStyles($xml);

        $this->assertCount(0, $result);
    }

    /**
     * @return void
     */
    public function testLoadWithInvalidStyle(): void
    {
        $xmlString = <<<XML
<styles>
    <style>pascal</style>
    <style>abc</style>
</styles>
XML;

        $xml = $this->loadXml($xmlString);

        $loader = new StyleLoader();
        $result = $loader->loadStyles($xml);

        $this->assertCount(2, $result);
    }
}
