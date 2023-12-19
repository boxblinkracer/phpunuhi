<?php

namespace phpunit\Components\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
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
     * @return void
     */
    protected function setUp(): void
    {
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
     * @throws Exception
     */
    public function testValidMixedCases(): void
    {
        $case1 = new CaseStyle('snake');
        $case2 = new CaseStyle('camel');

        $result = $this->validateSet(
            'global.businessEvents.mollie_checkout_order_success',
            [$case1, $case2]
        );

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidCases(): void
    {
        $case1 = new CaseStyle('snake');

        $result = $this->validateSet(
            'global.businessEvents.flowTitle',
            [$case1]
        );

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMixedWithLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('camel');
        $case2->setLevel(1);

        $case3 = new CaseStyle('snake');
        $case3->setLevel(2);

        $result = $this->validateSet(
            'global.businessEvents.mollie_checkout_order_success',
            [$case1, $case2, $case3]
        );

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * This test verifies that, if we assign levels, a missing level number
     * leads to a global validation.
     * This means both our ends are only snake case, while the middle part is camel.
     * This is only valid because level 0 + 2 are snake-validated, and the rest is globally validated with camel.
     *
     * @return void
     * @throws Exception
     */
    public function testMixedWithMissingLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('snake');
        $case2->setLevel(2);

        # this would be globally checked
        $case3 = new CaseStyle('camel');

        $result = $this->validateSet(
            'global_snake.businessEvents.other_snake',
            [$case1, $case2, $case3]
        );

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * This test verifies that if we have an existing level set
     * then a global style is not used to be verified.
     * Our first level is only pascal, but the content is snake.
     * Our global style is snake which would be valid, but our first level is pinned to be
     * pascal, so it's still not valid.
     *
     * @return void
     * @throws Exception
     */
    public function testGlobalStylesNotValidatedForExistingLevels(): void
    {
        $case1 = new CaseStyle('pascal');
        $case1->setLevel(0);

        $case2 = new CaseStyle('snake');

        $result = $this->validateSet(
            'global_snake.business_event',
            [$case1, $case2]
        );

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * This test verifies that all our levels are correctly checked.
     * Our 2nd level has an invalid style, so this should break.
     *
     * @return void
     * @throws Exception
     */
    public function testMixedWithInvalidLevels(): void
    {
        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $case2 = new CaseStyle('camel');
        $case2->setLevel(1);

        $case3 = new CaseStyle('pascal');   # INVALID
        $case3->setLevel(2);

        $result = $this->validateSet(
            'global.businessEvents.mollie_checkout_order_success',
            [$case1, $case2, $case3]
        );

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testValidCasesWithoutSetStyles(): void
    {
        $result = $this->validateSet(
            'global.businessEvents.mollie_checkout_order_success',
            []
        );

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @param string $translationKey
     * @param array $caseStyles
     * @return ValidationResult
     * @throws CaseStyleNotFoundException
     */
    private function validateSet(string $translationKey, array $caseStyles): ValidationResult
    {
        $storage = new JsonStorage(3, true);

        $locale = new Locale('en-GB', '', '');
        $locale->addTranslation($translationKey, 'Cancel', 'group1');

        $set = new TranslationSet(
            '',
            'json',
            new Protection(),
            [$locale],
            new Filter(),
            [],
            $caseStyles,
            []
        );

        return $this->validator->validate($set, $storage);
    }

}