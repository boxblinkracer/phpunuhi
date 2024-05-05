<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\LocalesPlaceholderProcessor;

class LocalesPlaceholderProcessorTest extends TestCase
{

    /**
     * @var LocalesPlaceholderProcessor
     */
    private $processor;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->processor = new LocalesPlaceholderProcessor();
    }


    /**
     * @return void
     */
    public function testEmptyFileNameReturnsEmptyString(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '',
            'not-empty',
            'not-empty'
        );

        $this->assertEquals('', $filename);
    }

    /**
     * @return void
     */
    public function testPlainFile(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            './en/data.xml',
            '',
            ''
        );

        $this->assertEquals('en/data.xml', $filename);
    }


    /**
     * @return void
     */
    public function testPlainFileWithAbsolutePath(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '/var/www/translations/en.json',
            '',
            ''
        );

        $this->assertEquals('/var/www/translations/en.json', $filename);
    }

    /**
     * @return void
     */
    public function testPlainFileWithAbsolutePathAndConfigWorkdir(): void
    {
        $filename = $this->processor->buildRealFilename(
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
     * @return void
     */
    public function testConfigDirectoryWorkDirBeforePlainFile(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            './en/data.xml',
            '',
            './sub-dir/config/config.xml'
        );

        $this->assertEquals('sub-dir/config/en/data.xml', $filename);
    }

    /**
     * @return void
     */
    public function testPlaceholderLocaleInFilename(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            './snippets/data-%locale%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-en.xml', $filename);
    }

    /**
     * @return void
     */
    public function testPlaceholderLocaleUCInFilename(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            './snippets/data-%locale_uc%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-EN.xml', $filename);
    }

    /**
     * @return void
     */
    public function testPlaceholderLocaleLCInFilename(): void
    {
        $filename = $this->processor->buildRealFilename(
            'EN',
            './snippets/data-%locale_lc%.xml',
            '',
            ''
        );

        $this->assertEquals('snippets/data-en.xml', $filename);
    }

    /**
     * @return void
     */
    public function testBasePathIsUsed(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '%base_path%/data.xml',
            './translations/administration',
            ''
        );

        $this->assertEquals('translations/administration/data.xml', $filename);
    }

    /**
     * @return void
     */
    public function testAbsoluteBasePathIsUsed(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '%base_path%/data.xml',
            '/translations/administration',
            ''
        );

        $this->assertEquals('/translations/administration/data.xml', $filename);
    }

    /**
     * @return void
     */
    public function testBasePathWithUpperDirectory(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '%base_path%/data.json',
            '../../translations/administration',
            '/var/www/html/devops'
        );

        $this->assertEquals('/var/www/html/../../translations/administration/data.json', $filename);
    }

    /**
     * @return void
     */
    public function testBasePathWithUpperDirectoryWithoutConfigFile(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            '%base_path%/data.json',
            '../../translations/administration',
            ''
        );

        $this->assertEquals('../../translations/administration/data.json', $filename);
    }

    /**
     * @return void
     */
    public function testBasePathIsSkippedIfNotUsed(): void
    {
        $filename = $this->processor->buildRealFilename(
            'en',
            'data.xml',
            './translations/administration',
            ''
        );

        $this->assertEquals('data.xml', $filename);
    }

    /**
     * @return void
     */
    public function testBasePathUsesLocalesPlaceholder(): void
    {
        $filename = $this->processor->buildRealFilename(
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
     * @return void
     */
    public function testPlaceholderLocaleUNInFilename(string $expect, string $locale): void
    {
        $filename = $this->processor->buildRealFilename(
            $locale,
            '%base_path%/storefront-%locale%.xml',
            './snippets/%locale_un%/',
            ''
        );

        $this->assertEquals("snippets/{$expect}/storefront-{$locale}.xml", $filename);
    }
}
