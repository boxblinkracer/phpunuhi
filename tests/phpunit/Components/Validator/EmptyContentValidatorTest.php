<?php

namespace phpunit\Components\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
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
        $this->validator = new EmptyContentValidator([]);
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('EMPTY_CONTENT', $this->validator->getTypeIdentifier());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testAllValid(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnCancel', 'Abbrechen', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
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
    public function testEmptyContentFound(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeDE->addTranslation('card.btnCancel', '', 'group1');
        $localeDE->addTranslation('card.btnOK', 'OK', 'group1');

        $localeEN = new Locale('en-GB', false, '', '');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');
        $localeEN->addTranslation('card.btnOK', '', ''); # also use without group

        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $result = $this->validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }


    /**
     * @throws Exception
     * @return void
     */
    public function testEmptyContentWithAllowList(): void
    {
        $localeDE = new Locale('de-DE', false, '', '');
        $localeEN = new Locale('en-GB', false, '', '');

        # allow empty in DE -> should be OK
        $localeDE->addTranslation('card.btnCancel', '', 'group1');
        $localeEN->addTranslation('card.btnCancel', 'Cancel', 'group1');

        # do not allow anything -> 1 error
        $localeDE->addTranslation('card.btnTitle', 'Title', 'group1');
        $localeEN->addTranslation('card.btnTitle', '', '');

        # allow empty in EN -> should be OK
        $localeDE->addTranslation('card.btnOK', 'OK', '');
        $localeEN->addTranslation('card.btnOK', '', 'group1');


        $set = $this->buildSet([$localeDE, $localeEN]);

        $storage = new JsonStorage();

        $allowList = [];
        $allowList[] = new AllowEmptyContent('card.btnCancel', ['de-DE']);
        $allowList[] = new AllowEmptyContent('card.btnOK', ['en-GB']);

        $validator = new EmptyContentValidator($allowList);

        $result = $validator->validate($set, $storage);

        $this->assertCount(1, $result->getErrors());
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
