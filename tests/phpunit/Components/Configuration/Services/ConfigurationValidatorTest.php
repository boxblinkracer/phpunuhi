<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\ConfigurationValidator;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Tests\Utils\Traits\TranslationSetBuilderTrait;

class ConfigurationValidatorTest extends TestCase
{
    use TranslationSetBuilderTrait;



    private ConfigurationValidator $validator;


    public function setUp(): void
    {
        $this->validator = new ConfigurationValidator();
    }

    /**
     * @throws ConfigurationException
     */
    public function testEmptySetsLeadToException(): void
    {
        $this->expectException(ConfigurationException::class);

        $configuration = new Configuration([]);

        $this->validator->validateConfig($configuration);
    }

    /**
     * @throws ConfigurationException
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
