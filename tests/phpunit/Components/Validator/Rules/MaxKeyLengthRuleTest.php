<?php

namespace phpunit\Components\Validator\Rules;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\MaxKeyLengthRule;
use PHPUnuhi\Models\Configuration\Filter;
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
     * @return void
     * @throws \Exception
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new MaxKeyLengthRule(20);

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
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new MaxKeyLengthRule(0);

        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function testKeyLengthExceeded(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card-long.longlonglong', '', 'group1');
        $localeDE->addTranslation('card-long.longlonglong2', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card-long.longlonglong', 'Cancel', 'group1');

        # this is the one we are looking to fail
        $localeEN->addTranslation('card-long.short', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new MaxKeyLengthRule(6);  # "short" +1

        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
     * @param int $keyLength
     * @return TranslationSet
     */
    private function buildSet(array $locales): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            $locales,
            new Filter(),
            [],
            [],
            []
        );
    }

}