<?php

namespace PHPUnuhi\Tests\Components\Validator\Rules;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\NestingDepthRule;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Tests\Utils\Fakes\FakeEmptyDelimiterStorage;

class NestingDepthRuleTest extends TestCase
{


    /**
     * @return void
     */
    public function getRuleIdentifier(): void
    {
        $validator = new NestingDepthRule(0);

        $this->assertEquals('NESTING', $validator->getRuleIdentifier());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new NestingDepthRule(20);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testNestingDepthNotAppliedOnSingleHierarchy(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', '');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4', 'Cancel', '');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new IniStorage();

        $validator = new NestingDepthRule(1);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testLengthZeroAlwaysValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new NestingDepthRule(0);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testNestingDepthReached(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4.level5', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new NestingDepthRule(4);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testStorageWithEmptyDelimiterIsValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $set = $this->buildSet([$localeDE]);

        $storage = new FakeEmptyDelimiterStorage();

        $validator = new NestingDepthRule(2);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }


    /**
     * @param Locale[] $locales
     * @return TranslationSet
     */
    private function buildSet(array $locales): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            new Protection(),
            $locales,
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );
    }
}
