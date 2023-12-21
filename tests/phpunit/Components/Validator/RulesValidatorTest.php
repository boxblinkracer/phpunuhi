<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\TranslationSetBuilderTrait;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Components\Validator\Rules\NestingDepthRule;
use PHPUnuhi\Components\Validator\RulesValidator;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class RulesValidatorTest extends TestCase
{

    use TranslationSetBuilderTrait;

    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $validator = new RulesValidator();

        $this->assertEquals('RULE', $validator->getTypeIdentifier());
    }


    /**
     * @return void
     */
    public function testValidationWithoutTranslationsIsOkay(): void
    {
        $storage = new JsonStorage();

        $locale = new Locale('de', '', '');

        $set = $this->buildTranslationSet(
            [
                $locale
            ],
            [
                new Rule(Rules::NESTING_DEPTH, 2),
            ]
        );

        $validator = new RulesValidator();
        $result = $validator->validate($set, $storage);

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @return array[]
     */
    public function ruleValidationDataProvider()
    {
        return [
            [Rules::NESTING_DEPTH, 2],
            [Rules::KEY_LENGTH, 5],
            [Rules::DISALLOWED_TEXT, 'Cancel'],
            [Rules::DUPLICATE_CONTENT, false],
        ];
    }

    /**
     * @dataProvider ruleValidationDataProvider
     *
     * @param string $ruleName
     * @param $ruleValue
     * @return void
     */
    public function testRulesCorrectlyValidate(string $ruleName, $ruleValue): void
    {
        $storage = new JsonStorage();

        $locale = new Locale('de', '', '');
        $locale->addTranslation('card.overview.btnCancel', 'Cancel', '');
        $locale->addTranslation('btnCancel', 'Cancel', '');

        $set = $this->buildTranslationSet(
            [
                $locale
            ],
            [
                new Rule($ruleName, $ruleValue),
            ]
        );


        $validator = new RulesValidator();
        $result = $validator->validate($set, $storage);

        $this->assertEquals(false, $result->isValid());
    }
}
