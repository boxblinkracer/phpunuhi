<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\LocalesPlaceholderProcessor;

class LocalesPlaceholderProcessorTest extends TestCase
{
    private LocalesPlaceholderProcessor $processor;



    public function setUp(): void
    {
        $this->processor = new LocalesPlaceholderProcessor();
    }



    public function testEmptyFileNameReturnsEmptyString(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '',
            'not-empty',
            'not-empty'
        );

        $this->assertEquals('', $filename);
    }


    public function testPlainFile(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            './en/data.xml',
            '',
            ''
        );

        $this->assertEquals('en/data.xml', $filename);
    }



    public function testPlainFileWithAbsolutePath(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '/var/www/translations/en.json',
            '',
            ''
        );

        $this->assertEquals('/var/www/translations/en.json', $filename);
    }


    public function testPlainFileWithAbsolutePathAndConfigWorkdir(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '/var/www/translations/en.json',
            '',
            'test.xml'
        );

        $this->assertEquals('/var/www/translations/en.json', $filename);
    }

    /**
     * If the locale filenames are provided as relative paths in the XML configuration,
     * then we need to resolve it correctly. This means the work-directory is based on the
     * directory the configuration file is in.
     *
     */
    public function testConfigDirectoryWorkDirBeforePlainFile(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            './en/data.xml',
            '',
            './sub-dir/config/config.xml'
        );

        $this->assertEquals('sub-dir/config/en/data.xml', $filename);
    }


    public function testPlaceholderLocaleInFilename(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            './snippets/data-%locale%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-en.xml', $filename);
    }


    public function testPlaceholderLocaleUCInFilename(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            './snippets/data-%locale_uc%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-EN.xml', $filename);
    }


    public function testPlaceholderLocaleLCInFilename(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'EN',
            './snippets/data-%locale_lc%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-en.xml', $filename);
    }


    public function testBasePathIsUsed(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '%base_path%/data.xml',
            './translations/administration',
            ''
        );

        $this->assertEquals('translations/administration/data.xml', $filename);
    }


    public function testAbsoluteBasePathIsUsed(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '%base_path%/data.xml',
            '/translations/administration',
            ''
        );

        $this->assertEquals('/translations/administration/data.xml', $filename);
    }


    public function testBasePathWithUpperDirectory(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '%base_path%/data.json',
            '../../translations/administration',
            '/var/www/html/devops'
        );

        $this->assertEquals('/var/www/html/../../translations/administration/data.json', $filename);
    }


    public function testBasePathWithUpperDirectoryWithoutConfigFile(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            '%base_path%/data.json',
            '../../translations/administration',
            ''
        );

        $this->assertEquals('../../translations/administration/data.json', $filename);
    }


    public function testBasePathIsSkippedIfNotUsed(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en',
            'data.xml',
            './translations/administration',
            ''
        );

        $this->assertEquals('data.xml', $filename);
    }


    public function testBasePathUsesLocalesPlaceholder(): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            'en-US',
            '%base_path%/data-%locale%.xml',
            './translations/%locale%/administration',
            ''
        );

        $this->assertEquals('translations/en-US/administration/data-en-US.xml', $filename);
    }

    /**
     * @testWith [ "fr_FR", "fr-FR" ]
     *           [ "fr_be", "fr-be" ]
     *           [ "fr_cH", "fr-cH" ]
     *           [ "fr", "fr" ]
     */
    public function testPlaceholderLocaleUNInFilename(string $expect, string $locale): void
    {
        $filename = $this->processor->buildRealLocaleFilename(
            $locale,
            '%base_path%/storefront-%locale%.xml',
            './snippets/%locale_un%/',
            ''
        );

        $this->assertEquals("snippets/{$expect}/storefront-{$locale}.xml", $filename);
    }
}
