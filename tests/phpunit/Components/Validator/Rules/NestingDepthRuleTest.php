<?php

namespace phpunit\Components\Validator\Rules;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\NestingDepthRule;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


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
     * @return void
     * @throws \Exception
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new NestingDepthRule(20);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testLengthZeroAlwaysValid(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new NestingDepthRule(0);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testNestingDepthReached(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('lvl1.lvl2.level3.level4', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('lvl1.lvl2.level3.level4.level5', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new NestingDepthRule(4);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
     * @param int $maxDepth
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
            [],
            []
        );
    }

}