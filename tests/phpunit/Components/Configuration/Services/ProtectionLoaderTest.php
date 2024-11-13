<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\ProtectionLoader;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Tests\Utils\Traits\XmlLoaderTrait;

class ProtectionLoaderTest extends TestCase
{
    use XmlLoaderTrait;


    public function testLoadProtection(): void
    {
        $xml = '
            <filterNode>
                <marker start="start1" end="end1"/>
                <marker start="start2" end="end2"/>
                <term>term1</term>
                <term>term2</term>
            </filterNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new ProtectionLoader();
        $actual = $loader->loadProtection($xmlNode);


        $expected = new Protection();
        $expected->addMarker('start1', 'end1');
        $expected->addMarker('start2', 'end2');
        $expected->addTerm('term1');
        $expected->addTerm('term2');

        $this->assertEquals($expected, $actual);
    }
}
