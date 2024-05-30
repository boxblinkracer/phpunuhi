<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\TranslationSetBuilderTrait;
use PHPUnuhi\Configuration\Services\ConfigurationValidator;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class ConfigurationValidatorTest extends TestCase
{
    use TranslationSetBuilderTrait;


    /**
     * @var ConfigurationValidator
     */
    private $validator;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->validator = new ConfigurationValidator();
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testEmptySetsLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $configuration = new Configuration([]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testSetsWithoutNameLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $set = new TranslationSet(
            '',
            '',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $configuration = new Configuration([$set]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testSetsWithoutFormatLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $set = new TranslationSet(
            'Storefront',
            '',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $configuration = new Configuration([$set]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testSetsWithSameNameLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $set = new TranslationSet(
            'Storefront',
            'json',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        $configuration = new Configuration([$set, $set]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testLocalesWithoutNameLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $locale = new Locale('', false, '', '');

        $set = $this->buildTranslationSet([$locale], []);

        $configuration = new Configuration([$set, $set]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testLocalesWithSameNameLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $locale = new Locale('DE', false, '', '');

        $set = $this->buildTranslationSet([$locale, $locale], []);

        $configuration = new Configuration([$set, $set]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testLocalesWithNotExistingFileLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $locale = new Locale('DE', false, 'not-existing.json', '');

        $set = $this->buildTranslationSet([$locale], []);

        $configuration = new Configuration([$set, $set]);

        $this->validator->validateConfig($configuration);
    }
}
