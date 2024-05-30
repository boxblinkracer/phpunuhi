<?php

namespace phpunit\Components\Validator\Rules;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeEmptyDelimiterStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\MaxKeyLengthRule;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class MaxKeyLengthRuleTest extends TestCase
{


    /**
     * @return void
     */
    public function getRuleIdentifier(): void
    {
        $validator = new MaxKeyLengthRule(0);

        $this->assertEquals('KEY_LENGTH', $validator->getRuleIdentifier());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new MaxKeyLengthRule(20);

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
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new MaxKeyLengthRule(0);

        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }


    /**
     * @throws Exception
     * @return void
     */
    public function testKeyLengthExceeded(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card-long.longlonglong', '', 'group1');
        $localeDE->addTranslation('card-long.longlonglong2', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card-long.longlonglong', 'Cancel', 'group1');

        # this is the one we are looking to fail
        $localeEN->addTranslation('card-long.short', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new MaxKeyLengthRule(6);  # "short" +1

        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testStorageWithEmptyDelimiterUsesNoNesting(): void
    {
        $keyWithDelimiter = 'card-long.longlonglong';

        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation($keyWithDelimiter, '', 'group1');

        $set = $this->buildSet([$localeDE]);

        $storage = new FakeEmptyDelimiterStorage();

        # now create a rule for our $keyWithDelimiter but 1 size smaller
        # so that it's not valid anymore
        $validator = new MaxKeyLengthRule(strlen($keyWithDelimiter) - 1);

        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
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
