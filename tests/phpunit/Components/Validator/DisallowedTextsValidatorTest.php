<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\DisallowedTextValidator;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class DisallowedTextsValidatorTest extends TestCase
{

    /**
     * @var DisallowedTextValidator
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new DisallowedTextValidator();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('DISALLOWED_TEXT', $this->validator->getTypeIdentifier());
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

        $set = $this->buildSet([$localeDE, $localeEN], []);

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testDisallowedTextFound(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', 'group1');

        $set = $this->buildSet(
            [$localeDE, $localeEN],
            [
                'Cancel'
            ]
        );

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
     * @param array $disallowedTexts
     * @return TranslationSet
     */
    private function buildSet(array $locales, array $disallowedTexts): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            $locales,
            new Filter(),
            [],
            [],
            [
                new Rule(Rules::DISALLOWED_TEXT, $disallowedTexts)
            ]
        );
    }

}