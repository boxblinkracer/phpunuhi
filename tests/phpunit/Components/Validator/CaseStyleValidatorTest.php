<?php

namespace phpunit\Components\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Models\Configuration\CaseStyle;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class CaseStyleValidatorTest extends TestCase
{

    /**
     * @var CaseStyleValidator
     */
    private $validator;
    /**
     * @var JsonStorage
     */
    private $storageJson;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new CaseStyleValidator();

        $this->storageJson = new JsonStorage();
    }


    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $this->assertEquals('CASE_STYLE', $this->validator->getTypeIdentifier());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testValidMixedCases(): void
    {
        $case1 = new CaseStyle('snake');
        $case2 = new CaseStyle('camel');

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testInvalidCases(): void
    {
        $case1 = new CaseStyle('snake');

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global.businessEvents.flowTitle', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testMixedWithLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('camel');
        $case2->setLevel(1);

        $case3 = new CaseStyle('snake');
        $case3->setLevel(2);

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * This test verifies that, if we assign levels, a missing level number
     * leads to a global validation.
     * This means both our ends are only snake case, while the middle part is camel.
     * This is only valid because level 0 + 2 are snake-validated, and the rest is globally validated with camel.
     *
     * @throws Exception
     * @return void
     */
    public function testMixedWithMissingLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('snake');
        $case2->setLevel(2);

        # this would be globally checked
        $case3 = new CaseStyle('camel');

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global_snake.businessEvents.other_snake', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * This test verifies that if we have an existing level set
     * then a global style is not used to be verified.
     * Our first level is only pascal, but the content is snake.
     * Our global style is snake which would be valid, but our first level is pinned to be
     * pascal, so it's still not valid.
     *
     * @throws Exception
     * @return void
     */
    public function testGlobalStylesNotValidatedForExistingLevels(): void
    {
        $case1 = new CaseStyle('pascal');
        $case1->setLevel(0);

        $case2 = new CaseStyle('snake');

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global_snake.business_event', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * This test verifies that all our levels are correctly checked.
     * Our 2nd level has an invalid style, so this should break.
     *
     * @throws Exception
     * @return void
     */
    public function testMixedWithInvalidLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('camel');
        $case2->setLevel(1);

        $case3 = new CaseStyle('pascal');   # INVALID
        $case3->setLevel(2);

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3]);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testValidCasesWithoutSetStyles(): void
    {
        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, []);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testSingleHierarchyValidation(): void
    {
        $storageINI = new IniStorage();

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation('btn-cancel', 'Cancel', '');

        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $set = $this->buildSet($locale, [$case1]);

        $result = $this->validator->validate($set, $storageINI);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @param Locale $locale
     * @param CaseStyle[] $caseStyles
     * @return TranslationSet
     */
    private function buildSet(Locale $locale, array $caseStyles): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            new Protection(),
            [$locale],
            new Filter(),
            [],
            $caseStyles,
            []
        );
    }
}
