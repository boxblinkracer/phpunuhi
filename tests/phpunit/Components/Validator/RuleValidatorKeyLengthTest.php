<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\RuleValidatorDisallowedTexts;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Components\Validator\RuleValidatorKeyLength;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class RuleValidatorKeyLengthTest extends TestCase
{

    /**
     * @var RuleValidatorKeyLength
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new RuleValidatorKeyLength();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('KEY_LENGTH', $this->validator->getTypeIdentifier());
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

        $set = $this->buildSet([$localeDE, $localeEN], 20);

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

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

        $set = $this->buildSet([$localeDE, $localeEN], 6); # "short" +1

        $storage = new JsonStorage(3, true);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $locales
     * @param int $keyLength
     * @return TranslationSet
     */
    private function buildSet(array $locales, int $keyLength): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            $locales,
            new Filter(),
            [],
            [],
            [
                new Rule(Rules::KEY_LENGTH, $keyLength)
            ]
        );
    }

}