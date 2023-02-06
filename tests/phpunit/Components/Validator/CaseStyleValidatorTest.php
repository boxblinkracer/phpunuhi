<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;


class CaseStyleValidatorTest extends TestCase
{

    /**
     * @var Locale
     */
    private $localeDE;

    /**
     * @var Locale
     */
    private $localeEN;

    /**
     * @var CaseStyleValidator
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->localeDE = new Locale('de-DE', '', '');
        $this->localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');
        $this->localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $this->localeEN = new Locale('en-GB', '', '');
        $this->localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $this->localeEN->addTranslation('card.btnOK', 'OK', 'group1');

        $this->validator = new CaseStyleValidator();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('CASE_STYLE', $this->validator->getTypeIdentifier());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidCases(): void
    {
        $storage = new JsonStorage(3, true);
        $set = $this->buildSet(['snake', 'camel']);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testInvalidCases(): void
    {
        $storage = new JsonStorage(3, true);
        $set = $this->buildSet(['snake']);

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @param array $caseStyles
     * @return TranslationSet
     */
    private function buildSet(array $caseStyles): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            [$this->localeEN, $this->localeDE],
            new Filter(),
            [],
            $caseStyles
        );
    }

}