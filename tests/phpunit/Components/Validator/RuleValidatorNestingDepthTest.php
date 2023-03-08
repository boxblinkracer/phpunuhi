<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\RuleValidatorDisallowedTexts;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Components\Validator\RuleValidatorKeyLength;
use PHPUnuhi\Components\Validator\RuleValidatorNestingDepth;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class RuleValidatorNestingDepthTest extends TestCase
{

    /**
     * @var RuleValidatorNestingDepth
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new RuleValidatorNestingDepth();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('NESTING', $this->validator->getTypeIdentifier());
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

        $set = $this->buildSet([$localeDE, $localeEN], 20);

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

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

        $set = $this->buildSet([$localeDE, $localeEN], 4);

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
     * @param int $maxDepth
     * @return TranslationSet
     */
    private function buildSet(array $locales, int $maxDepth): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            $locales,
            new Filter(),
            [],
            [],
            [
                new Rule(Rules::NESTING_DEPTH, $maxDepth)
            ]
        );
    }

}