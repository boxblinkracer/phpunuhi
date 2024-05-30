<?php

namespace phpunit\Components\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2], []);

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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global.businessEvents.flowTitle', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1], []);

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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3], []);

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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global_snake.businessEvents.other_snake', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3], []);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * This test verifies that if we have an existing level set,
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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global_snake.business_event', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2], []);

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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1, $case2, $case3], []);

        $result = $this->validator->validate($set, $this->storageJson);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * This test verifies if we only have 1 case style on level 1,
     * and we have invalid key, that it fails correctly.
     *
     * @throws CaseStyleNotFoundException
     * @return void
     */
    public function testOnlyLevel1IsSetAndFails(): void
    {
        $case1 = new CaseStyle('kebab');
        $case1->setLevel(1);

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('card-section.lblTitle', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [$case1], []);

        $result = $this->validator->validate($set, $this->storageJson);

        $firstError = $result->getErrors()[0];

        $this->assertEquals(false, $result->isValid());

        # make sure the correct error is found
        # we only have an error at level 1, not at 0
        $this->assertEquals("Invalid case-style for part 'lblTitle' in key 'card-section.lblTitle' at level: 1", $firstError->getFailureMessage());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testValidCasesWithoutSetStyles(): void
    {
        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('global.businessEvents.mollie_checkout_order_success', 'Cancel', 'group1');

        $set = $this->buildSet($locale, [], []);

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

        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('btn-cancel', 'Cancel', '');

        $case1 = new CaseStyle('snake');
        $case1->setLevel(0);

        $set = $this->buildSet($locale, [$case1], []);

        $result = $this->validator->validate($set, $storageINI);

        $this->assertEquals(false, $result->isValid());
    }

    /**
     * This test verifies that a No-FQP (FULLY QUALIFIED PATH) scope allows us to only provide a part of the key.
     * This means if we have nested structures, we can only provide e.g. the last part of the structure -
     * so the plain key name.
     * If we provide only the name of the key, the validation is OK, because we successfully
     * recognize and ignore the key in its found structure.
     * Also, if we provide the full key, it will be ignored and therefore the validation is also OK.
     *
     * @testWith   [ true, "root.sub.IGNORE_THIS" ]
     *             [ true, "IGNORE_THIS" ]
     *             [ false, "DIFFERENT_KEY" ]
     *
     * @param bool $isValid
     * @param string $ignoreKey
     * @throws CaseStyleNotFoundException
     * @return void
     */
    public function testIgnoreWrongKeyNoFQP(bool $isValid, string $ignoreKey): void
    {
        $ignoreKey = new CaseStyleIgnoreKey($ignoreKey, false);

        $notCamelCaseKey = 'root.sub.IGNORE_THIS';

        $storageINI = new IniStorage();
        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('thisIsCamel', 'Cancel', '');
        $locale->addTranslation($notCamelCaseKey, 'Cancel', '');
        $case1 = new CaseStyle('camel');

        $set = $this->buildSet($locale, [$case1], [$ignoreKey]);

        $validator = new CaseStyleValidator();
        $result = $validator->validate($set, $storageINI);

        $this->assertEquals($isValid, $result->isValid());
    }

    /**
     * This test verifies that a FQP (FULLY QUALIFIED PATH) scope considers the full provided key.
     * If we have nested structures, we need to provide the full nested key.
     * If only a part of the key is provided, it will NOT be ignored and therefore fail,
     * because we do not use camel-case for this key.
     *
     * @testWith   [ true, "root.sub.IGNORE_THIS" ]
     *             [ false, "IGNORE_THIS" ]
     *             [ false, "DIFFERENT_KEY" ]
     *
     * @param bool $isValid
     * @param string $ignoreKey
     * @throws CaseStyleNotFoundException
     * @return void
     */
    public function testIgnoreWrongKeyScopeFQP(bool $isValid, string $ignoreKey): void
    {
        $ignoreKey = new CaseStyleIgnoreKey($ignoreKey, true);

        $notCamelCaseKey = 'root.sub.IGNORE_THIS';

        $storageINI = new IniStorage();
        $locale = new Locale('en-GB', false, '', '');
        $locale->addTranslation('thisIsCamel', 'Cancel', '');
        $locale->addTranslation($notCamelCaseKey, 'Cancel', '');
        $case1 = new CaseStyle('camel');

        $set = $this->buildSet($locale, [$case1], [$ignoreKey]);

        $validator = new CaseStyleValidator();
        $result = $validator->validate($set, $storageINI);

        $this->assertEquals($isValid, $result->isValid());
    }

    /**
     * @param Locale $locale
     * @param CaseStyle[] $caseStyles
     * @param CaseStyleIgnoreKey[] $ignoreCaseKeys
     * @return TranslationSet
     */
    private function buildSet(Locale $locale, array $caseStyles, array $ignoreCaseKeys): TranslationSet
    {
        return new TranslationSet(
            '',
            'json',
            new Protection(),
            [$locale],
            new Filter(),
            [],
            new CaseStyleSetting($caseStyles, $ignoreCaseKeys),
            []
        );
    }
}
