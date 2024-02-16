<?php

namespace phpunit\Components\Validator\Rules;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\DisallowedTextsRule;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class DisallowedTextsRuleTest extends TestCase
{


    /**
     * @return void
     */
    public function getRuleIdentifier(): void
    {
        $validator = new DisallowedTextsRule([]);

        $this->assertEquals('DISALLOWED_TEXT', $validator->getRuleIdentifier());
    }

    /**
     * @throws Exception
     * @return void
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

        $storage = new JsonStorage();

        $validator = new DisallowedTextsRule([]);
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testDisallowedTextFound(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $validator = new DisallowedTextsRule(
            [
                'Cancel'
            ]
        );

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
