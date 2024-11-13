<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\StyleLoader;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;
use PHPUnuhi\Tests\Utils\Traits\XmlLoaderTrait;

class StyleLoaderTest extends TestCase
{
    use XmlLoaderTrait;



    private StyleLoader $loader;



    public function setUp(): void
    {
        $this->loader = new StyleLoader();
    }


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


    public function testLoadWithoutStylesNode(): void
    {
        $xml = $this->loadXml('<root></root>');

        $result = $this->loader->loadStyles($xml);

        $this->assertCount(0, $result);
    }


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


    public function testMissingStyleEntriesAreWorking(): void
    {
        $xml = $this->loadXml('<styles></styles>');

        $result = $this->loader->loadStyles($xml);

        $this->assertCount(0, $result);
    }



    public function testLoadIgnores(): void
    {
        $xml = $this->loadXml('
            <styles>
                <ignore>
                    <key fqp="false">lblGlobal</key>
                    <key fqp="true">lblFixed</key>
                </ignore>
            </styles>
        ');

        $result = $this->loader->loadIgnoredKeys($xml);

        $this->assertCount(2, $result);

        $this->assertInstanceOf(CaseStyleIgnoreKey::class, $result[0]);
        $this->assertInstanceOf(CaseStyleIgnoreKey::class, $result[1]);

        $this->assertEquals('lblGlobal', $result[0]->getKey());
        $this->assertEquals(false, $result[0]->isFullyQualifiedPath(), 'FQP should be false');

        $this->assertEquals('lblFixed', $result[1]->getKey());
        $this->assertEquals(true, $result[1]->isFullyQualifiedPath(), 'FQP should be true');
    }


    public function testLoadIgnoresDefaultFQPIsTrue(): void
    {
        $xml = $this->loadXml('
            <styles>
                <ignore>
                    <key>lblTitle</key>
                </ignore>
            </styles>
        ');

        $result = $this->loader->loadIgnoredKeys($xml);

        $this->assertInstanceOf(CaseStyleIgnoreKey::class, $result[0]);
        $this->assertEquals(true, $result[0]->isFullyQualifiedPath(), 'FQP should be true if not provided');
    }


    public function testLoadIgnoresWithoutStylesNode(): void
    {
        $xml = $this->loadXml('<root></root>');

        $result = $this->loader->loadIgnoredKeys($xml);

        $this->assertCount(0, $result);
    }


    public function testLoadIgnoresWithMissingNode(): void
    {
        $xml = $this->loadXml('<styles></styles>');

        $result = $this->loader->loadIgnoredKeys($xml);

        $this->assertCount(0, $result);
    }
}
