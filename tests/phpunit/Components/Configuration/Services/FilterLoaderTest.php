<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\XmlLoaderTrait;
use PHPUnuhi\Configuration\Services\FilterLoader;
use PHPUnuhi\Models\Configuration\Filter;

class FilterLoaderTest extends TestCase
{
    use XmlLoaderTrait;


    /**
     * @return void
     */
    public function testLoadFilter(): void
    {
        $xml = '
            <filterNode>
                <include>
                    <key>allowed_key1</key>
                    <key>allowed_key2</key>
                </include>
                <exclude>
                    <key>excluded_key1</key>
                    <key>excluded_key2</key>
                </exclude>
            </filterNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new FilterLoader();
        $actual = $loader->loadFilter($xmlNode);


        $expected = new Filter();
        $expected->addIncludeKey('allowed_key1');
        $expected->addIncludeKey('allowed_key2');
        $expected->addExcludeKey('excluded_key1');
        $expected->addExcludeKey('excluded_key2');

        $this->assertEquals($expected, $actual);
    }
}
