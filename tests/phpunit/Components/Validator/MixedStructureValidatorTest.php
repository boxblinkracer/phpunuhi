<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\MixedStructureValidator;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Output\NullOutput;


class MixedStructureValidatorTest extends TestCase
{

    /**
     * @var MixedStructureValidator
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new MixedStructureValidator(new NullOutput());
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

        $isValid = $this->validator->validate($set, $storage);

        $this->assertEquals(true, $isValid);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testMixedStructureFound(): void
    {
        $localeDE = new Locale('de-DE', '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');

        $localeEN = new Locale('en-GB', '', '');
        $localeEN->addTranslation('card.btnCancel2', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', 'group1');

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage(3, true);


        $isValid = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $isValid);
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
            $locales,
            new Filter(),
            [],
            []
        );
    }

}