<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class EmptyContentValidatorTest extends TestCase
{

    /**
     * @var EmptyContentValidator
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new EmptyContentValidator();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('EMPTY_CONTENT', $this->validator->getTypeIdentifier());
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

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testEmptyContentFound(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

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