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
     * @var StyleLoader
     */
    private $loader;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->loader = new StyleLoader();
    }

    /**
     * @return void
     */
    public function testLoadStyles(): void
    {
        $xml = $this->loadXml('
            <styles>
                <style level="1">camel</style>
                <style>pascal</style>
                <style level="2">kebab</style>
            </styles>
        ');

        $result = $this->loader->loadStyles($xml);

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
        $xml = $this->loadXml('<root></root>');

        $result = $this->loader->loadStyles($xml);

        $this->assertCount(0, $result);
    }

    /**
     * @return void
     */
    public function testLoadWithInvalidStyle(): void
    {
        $xml = $this->loadXml('
            <styles>
                <style>pascal</style>
                <style>abc</style>
            </styles>
        ');

        $result = $this->loader->loadStyles($xml);

        $this->assertCount(2, $result);
    }

    /**
     * @return void
     */
    public function testMissingStyleEntriesAreWorking(): void
    {
        $xml = $this->loadXml('<styles></styles>');

        $result = $this->loader->loadStyles($xml);

        $this->assertCount(0, $result);
    }
}
