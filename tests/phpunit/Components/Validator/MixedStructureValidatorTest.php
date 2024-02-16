<?php

namespace phpunit\Components\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\MissingStructureValidator;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class MixedStructureValidatorTest extends TestCase
{

    /**
     * @var MissingStructureValidator
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new MissingStructureValidator();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('STRUCTURE', $this->validator->getTypeIdentifier());
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

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testMixedStructureFound(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel2', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();


        $result = $this->validator->validate($set, $storage);

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
