<?php

namespace phpunit\Components\Validator\Rules;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\DuplicateContentRule;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class DuplicateContentRuleTest extends TestCase
{

    /**
     * @return void
     */
    public function getRuleIdentifier(): void
    {
        $validator = new DuplicateContentRule();

        $this->assertEquals('DUPLICATE_CONTENT', $validator->getRuleIdentifier());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new DuplicateContentRule();

        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testDuplicateInDifferentLocalesIsValid(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new DuplicateContentRule();

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
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');
        $localeDE->addTranslation('card.btnOK2', 'Okay', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');
        $localeEN->addTranslation('card.btnOK2', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $validator = new DuplicateContentRule();

        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
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