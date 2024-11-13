<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Translation;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class TranslationSetTest extends TestCase
{
    public function testName(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertEquals('storefront', $set->getName());
    }


    public function testFormat(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];


        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertEquals('json', $set->getFormat());
    }

    /**
     * @throws Exception
     */
    public function testProtection(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();

        $protection->addTerm('protected-word');

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertEquals('protected-word', $set->getProtection()->getTerms()[0]);
    }

    /**
     * @throws Exception
     */
    public function testRules(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), $rules);

        $this->assertEquals('test-rule', $set->getRules()[0]->getName());
    }

    /**
     * @throws Exception
     */
    public function testHasRuleTrue(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), $rules);

        $this->assertTrue($set->hasRule('test-rule'));
    }

    /**
     * @throws Exception
     */
    public function testHasRuleFalse(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), $rules);

        $this->assertFalse($set->hasRule('abc'));
    }

    /**
     * @throws Exception
     */
    public function testGetRule(): void
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), $rules);

        $foundRule = $set->getRule('test-rule');

        $this->assertEquals('test-rule', $foundRule->getName());
    }

    /**
     * @throws Exception
     */
    public function testGetRuleNotFoundThrowsException(): void
    {
        $this->expectException(Exception::class);

        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, new CaseStyleSetting([], []), $rules);

        $set->getRule('abc');
    }


    public function testAttributes(): void
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');
        $attributes[] = new Attribute('sort', 'true');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $expected = [
            new Attribute('indent', '2'),
            new Attribute('sort', 'true'),
        ];

        $this->assertEquals($expected, $set->getAttributes());
    }


    public function testAttributeValue(): void
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertEquals('2', $set->getAttributeValue('indent'));
    }


    public function testGetAttributeValueNotFound(): void
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertEquals('', $set->getAttributeValue('abc'));
    }


    public function testGetLocales(): void
    {
        $attributes = [];
        $filter = new Filter();

        $locales = [];
        $locales[] = new Locale('', false, '', '');
        $locales[] = new Locale('', false, '', '');

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $this->assertCount(2, $set->getLocales());
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testFindAnyExistingTranslation(): void
    {
        $attributes = [];
        $filter = new Filter();


        $localeEN = new Locale('EN', false, '', '');

        $localeDE = new Locale('DE', false, '', '');
        $localeDE->addTranslation('btnCancel', 'Abbrechen', '');

        $locales = [$localeEN, $localeDE];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $existing = $set->findAnyExistingTranslation('btnCancel', '');

        $expected = [
            'locale' => 'DE',
            'translation' => $localeDE->findTranslation('btnCancel'), # we have to get the DE version
        ];

        $this->assertEquals($expected, $existing);
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testFindAnyExistingTranslationWithLocale(): void
    {
        $attributes = [];
        $filter = new Filter();


        $localeEN = new Locale('EN', false, '', '');
        $localeEN->addTranslation('btnCancel', 'Cancel', '');

        $localeDE = new Locale('DE', false, '', '');
        $localeDE->addTranslation('btnCancel', 'Abbrechen', '');

        $localeNL = new Locale('NL', false, '', '');
        $localeNL->addTranslation('btnCancel', 'Annuleren', '');

        $locales = [$localeEN, $localeDE, $localeNL];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $existing = $set->findAnyExistingTranslation('btnCancel', 'NL');

        $expected = [
            'locale' => 'NL',
            'translation' => $localeNL->findTranslation('btnCancel'),
        ];

        $this->assertEquals($expected, $existing);
    }

    /**
     * @throws Exception
     */
    public function testFindAnyExistingTranslationNotFound(): void
    {
        $this->expectException(TranslationNotFoundException::class);

        $attributes = [];
        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $set->findAnyExistingTranslation('abc', '');
    }

    /**
     * @throws Exception
     */
    public function testFindAnyExistingTranslationThrowsErrorOnEmptyValues(): void
    {
        $this->expectException(TranslationNotFoundException::class);

        $localeEN = new Locale('EN', false, '', '');
        $localeEN->addTranslation('btnCancel', '', '');

        $locales = [$localeEN];
        $attributes = [];
        $filter = new Filter();

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $set->findAnyExistingTranslation('btnCancel', '');
    }

    /**
     * This test verifies that the isCompletelyTranslated() works correctly.
     * We start with a EN translation, the DE one is missing, so it's not completely translated.
     * Then we add a translation that is invalid and last but not least we add a valid translation.
     *
     */
    public function testIsCompletelyTranslated(): void
    {
        $localeEN = new Locale('EN', false, '', '');
        $localeEN->addTranslation('btnCancel', 'Cancel', '');

        $localeDE = new Locale('DE', false, '', '');

        $locales = [$localeEN, $localeDE];

        $set = new TranslationSet(
            'storefront',
            'json',
            new Protection(),
            $locales,
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $isTranslated = $set->isCompletelyTranslated('btnCancel');
        $this->assertFalse($isTranslated, 'btnCancel is not existing in DE');

        $localeDE->addTranslation('btnCancel', '', '');

        $isTranslated = $set->isCompletelyTranslated('btnCancel');
        $this->assertFalse($isTranslated, 'btnCancel is still empty');

        $localeDE->addTranslation('btnCancel', 'Abbrechen', '');

        $isTranslated = $set->isCompletelyTranslated('btnCancel');
        $this->assertTrue($isTranslated);
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testFindAnyExistingTranslationSkipsWrongIDs(): void
    {
        $attributes = [];
        $filter = new Filter();

        $localeEN = new Locale('EN', false, '', '');
        $localeEN->addTranslation('1', 'Cancel', '');
        $localeEN->addTranslation('2', 'Cancel', '');
        $localeEN->addTranslation('3', 'Cancel', '');
        $localeEN->addTranslation('btnCancel', 'Cancel', '');

        $locales = [$localeEN];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $existing = $set->findAnyExistingTranslation('btnCancel', 'EN');

        $expected = [
            'locale' => 'EN',
            'translation' => new Translation('btnCancel', 'Cancel', '')
        ];

        $this->assertEquals($expected, $existing);
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testFindAnyExistingTranslationPrioritizesBaseLocale(): void
    {
        $attributes = [];
        $filter = new Filter();

        $locale1 = new Locale('EN', false, '', '');
        $locale1->addTranslation('btnCancel', 'Cancel', '');

        # the second locale is our base locale
        $locale2 = new Locale('DE', true, '', '');
        $locale2->addTranslation('btnCancel', 'Abbrechen', '');

        # this locale only has empty translation
        $locale3 = new Locale('ES', false, '', '');
        $locale3->addTranslation('btnCancel', '', '');

        $locales = [$locale1, $locale2, $locale3];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $existing = $set->findAnyExistingTranslation('btnCancel', 'EN');

        $expected = [
            'locale' => 'DE',
            'translation' => new Translation('btnCancel', 'Abbrechen', '')
        ];

        $this->assertEquals($expected, $existing);
    }


    public function testGetCasingStyle(): void
    {
        $pascalCase = new CaseStyle('pascal');

        $camelCase = new CaseStyle('camel');
        $camelCase->setLevel(2);

        $set = new TranslationSet(
            'storefront',
            'json',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([$camelCase, $pascalCase], []),
            []
        );

        $this->assertEquals('pascal', $set->getCasingStyle(0));
        $this->assertEquals('pascal', $set->getCasingStyle(1));
        $this->assertEquals('camel', $set->getCasingStyle(2));
    }


    public function testGetCasingStyleReturnEmptyIfNotFound(): void
    {
        $set = new TranslationSet(
            'storefront',
            'json',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $this->assertEquals('', $set->getCasingStyle(0));
        $this->assertEquals('', $set->getCasingStyle(1));
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testGetInvalidTranslationIDs(): void
    {
        $attributes = [];
        $filter = new Filter();

        $localeEN = new Locale('EN', false, '', '');
        $localeEN->addTranslation('lblSave', 'Cancel', '');
        $localeEN->addTranslation('lblCancel', '', '');
        $localeEN->addTranslation('lblTitle', '', '');

        $localeDE = new Locale('DE', false, '', '');
        $localeDE->addTranslation('lblSave', 'Abbrechen', '');
        $localeDE->addTranslation('lblCancel', '', '');
        $localeDE->addTranslation('lblTitle', 'Titel', '');

        $locales = [$localeEN, $localeDE];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, new CaseStyleSetting([], []), []);

        $existing = $set->getInvalidTranslationsIDs();

        $expected = [
            'lblCancel',
        ];

        $this->assertEquals($expected, $existing);
    }
}
