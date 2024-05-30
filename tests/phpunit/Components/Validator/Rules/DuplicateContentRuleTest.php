<?php

namespace phpunit\Components\Validator\Rules;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\DuplicateContent\DuplicateContent;
use PHPUnuhi\Components\Validator\Rules\DuplicateContentRule;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
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
        $validator = new DuplicateContentRule([]);

        $this->assertEquals('DUPLICATE_CONTENT', $validator->getRuleIdentifier());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testDuplicateContentNotAllowedInSingleHiearchyStorage(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('btn-Cancel1', 'Abbrechen', 'group1');
        $localeDE->addTranslation('btn-Cancel2', 'Abbrechen', 'group1');

        $set = $this->buildSet([$localeDE]);

        $storageSingleLevel = new IniStorage();

        $validator = new DuplicateContentRule(
            [
                new DuplicateContent('*', false)
            ]
        );

        $result = $validator->validate($set, $storageSingleLevel);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testNoDuplicatesMeansValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnCancel1', 'Abbrechen1', 'group1');
        $localeDE->addTranslation('card.btnCancel2', 'Abbrechen2', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnCancel1', 'Cancel1', 'group1');
        $localeEN->addTranslation('card.btnCancel2', 'Cancel2', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new DuplicateContentRule(
            [
                new DuplicateContent('*', false)
            ]
        );

        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testDuplicateInDifferentLocalesIsValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new DuplicateContentRule(
            [
                new DuplicateContent('*', false)
            ]
        );

        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }


    /**
     * @throws Exception
     * @return void
     */
    public function testWithLocaleAndFallbackWildcard(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');
        $localeDE->addTranslation('card.btnCancel', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');
        $localeEN->addTranslation('card.btnCancel', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new DuplicateContentRule(
            [
                new DuplicateContent('de', true),
                new DuplicateContent('*', false)
            ]
        );

        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testNoDuplicateContentRequiredForLocale(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');
        $localeDE->addTranslation('card.btnCancel', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnOK', 'OK', 'group1');
        $localeEN->addTranslation('card.btnCancel', 'OK', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new DuplicateContentRule(
            [
                new DuplicateContent('es', true),
            ]
        );

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
