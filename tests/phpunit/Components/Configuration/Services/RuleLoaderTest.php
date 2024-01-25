<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\XmlLoaderTrait;
use PHPUnuhi\Components\Validator\DuplicateContent\DuplicateContent;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Configuration\Services\RulesLoader;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;

class RuleLoaderTest extends TestCase
{
    use XmlLoaderTrait;


    /**
     * @return void
     */
    public function testNestingRuleLoaded(): void
    {
        $xml = '
            <rulesNode>
                <nestingDepth>5</nestingDepth>
            </rulesNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new RulesLoader();
        $actualRules = $loader->loadRules($xmlNode);


        $expectedRules = [
            new Rule(Rules::NESTING_DEPTH, '5'),
        ];

        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * @return void
     */
    public function testKeyLengthRuleLoaded(): void
    {
        $xml = '
            <rulesNode>
                <keyLength>10</keyLength>
            </rulesNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new RulesLoader();
        $actualRules = $loader->loadRules($xmlNode);


        $expectedRules = [
            new Rule(Rules::KEY_LENGTH, '10'),
        ];

        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * @return void
     */
    public function testDisallowedTextsRuleLoaded(): void
    {
        $xml = '
            <rulesNode>
                <disallowedTexts>
                    <text>badword1</text>
                    <text>badword2</text>
                </disallowedTexts>
            </rulesNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new RulesLoader();
        $actualRules = $loader->loadRules($xmlNode);


        $expectedRules = [
            new Rule(Rules::DISALLOWED_TEXT, ['badword1', 'badword2']),
        ];

        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * @return void
     */
    public function testDuplicateContentRuleLoaded(): void
    {
        $xml = '
            <rulesNode>
                <duplicateContent>
                    <locale name="*">false</locale>
                    <locale name="en">true</locale>
                </duplicateContent>
            </rulesNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new RulesLoader();
        $actualRules = $loader->loadRules($xmlNode);


        $expectedRules = [
            new Rule(Rules::DUPLICATE_CONTENT, [
                new DuplicateContent('*', false),
                new DuplicateContent('en', true),
            ]),
        ];

        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * @return void
     */
    public function testEmptyContentRuleLoaded(): void
    {
        $xml = '
            <rulesNode>
                <emptyContent>
                    <key name="btn.cancel">
                        <locale>en</locale>
                        <locale>de</locale>
                    </key>
                </emptyContent>
            </rulesNode>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new RulesLoader();
        $actualRules = $loader->loadRules($xmlNode);


        $expectedRules = [
            new Rule(Rules::EMPTY_CONTENT, [
                new AllowEmptyContent('btn.cancel', ['en', 'de']),
            ]),
        ];

        $this->assertEquals($expectedRules, $actualRules);
    }
}
