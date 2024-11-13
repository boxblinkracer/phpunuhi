<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\LocalesLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Tests\Utils\Traits\XmlLoaderTrait;

class LocalesLoaderTest extends TestCase
{
    use XmlLoaderTrait;


    private string $existingLocaleFile;


    private LocalesLoader $loader;



    protected function setUp(): void
    {
        $this->existingLocaleFile = __DIR__ . '/tmp_locale_file.json';

        $this->loader = new LocalesLoader();
    }


    protected function tearDown(): void
    {
        if (file_exists($this->existingLocaleFile)) {
            unlink($this->existingLocaleFile);
        }
    }

    /**
     * @throws ConfigurationException
     */
    public function testLocalesLoadedCorrectly(): void
    {
        $xmlNode = $this->loadXml('
            <locales basePath="./snippets">
                <locale name="en">' . $this->existingLocaleFile . '</locale>
                <locale name="de">' . $this->existingLocaleFile . '</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(2, $locales);

        $this->assertEquals('en', $locales[0]->getName());
        $this->assertEquals($this->existingLocaleFile, $locales[0]->getFilename());

        $this->assertEquals('de', $locales[1]->getName());
    }

    /**
     * This test verifies that we also load locales where the file does not exist.
     * Our ConfigurationValidator should then throw an exception, but the
     * locale should at least be loaded correclty.
     *
     * @throws ConfigurationException
     */
    public function testLocaleWithoutExistingFileIsAlsoLoaded(): void
    {
        $xmlNode = $this->loadXml('
            <locales>
                <locale name="en">./not-existing.json</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(1, $locales);
        $this->assertEquals('not-existing.json', $locales[0]->getFilename());
    }

    /**
     * @throws ConfigurationException
     */
    public function testBaseLocaleAttributeIsLoaded(): void
    {
        $xmlNode = $this->loadXml('
            <locales basePath="./snippets">
                <locale name="en">' . $this->existingLocaleFile . '</locale>
                <locale name="de" base="true">' . $this->existingLocaleFile . '</locale>
                <locale name="fr" base="false">' . $this->existingLocaleFile . '</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(3, $locales);

        $this->assertEquals('en', $locales[0]->getName());
        $this->assertEquals(false, $locales[0]->isBase());

        $this->assertEquals('de', $locales[1]->getName());
        $this->assertEquals(true, $locales[1]->isBase());

        $this->assertEquals('fr', $locales[2]->getName());
        $this->assertEquals(false, $locales[2]->isBase());
    }

    /**
     * @throws ConfigurationException
     */
    public function testBaseLocaleAttributeCannotBeDefinedTwice(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Only 1 locale can be defined as the base locale within a translation-set');

        $xmlNode = $this->loadXml('
            <locales basePath="./snippets">
                <locale name="en" base="true">' . $this->existingLocaleFile . '</locale>
                <locale name="de" base="true">' . $this->existingLocaleFile . '</locale>
            </locales>
        ');

        $this->loader->loadLocales($xmlNode, 'test.xml');
    }

    /**
     * @throws ConfigurationException
     */
    public function testBasePathPlaceholder(): void
    {
        $xmlNode = $this->loadXml('
            <locales basePath="./snippets">
                <locale name="en">%base_path%/translation.json</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('snippets/translation.json', $locales[0]->getFilename());
    }

    /**
     * @testWith  [ "" ]
     *            [ " " ]
     *
     * @throws ConfigurationException
     */
    public function testInvalidBasePathPlaceholdersAreSkipped(string $placeholder): void
    {
        $xmlNode = $this->loadXml('
            <locales basePath="' . $placeholder . '">
                <locale name="en">%base_path%/translation.json</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('%base_path%/translation.json', $locales[0]->getFilename());
    }

    /**
     * @testWith     [ "en-US", "%locale%", "en-US" ]
     *               [ "EN", "%locale_uc%", "en" ]
     *               [ "en", "%locale_lc%", "EN" ]
     *               [ "fr_Fr", "%locale_un%", "fr-Fr" ]
     *
     * @throws ConfigurationException
     */
    public function testLocalePlaceholder(string $expectedLocalePart, string $placeholder, string $locale): void
    {
        $xmlNode = $this->loadXml('
            <locales>
                <locale name="' . $locale . '">' . $placeholder . '/translation.json</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals($expectedLocalePart . '/translation.json', $locales[0]->getFilename());
    }

    /**
     * @throws ConfigurationException
     */
    public function testIniSectionLoaded(): void
    {
        $xmlNode = $this->loadXml('
            <locales>
                <locale name="en" iniSection="en-GB">%locale%/translation.ini</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('en-GB', $locales[0]->getIniSection());
    }

    /**
     * This test verifies that we also load locales where the file does not exist.
     * The ConfigurationValidator does then need to throw an exception for this.
     *
     * @throws ConfigurationException
     */
    public function testLocaleWithoutNameIsLoaded(): void
    {
        $xmlNode = $this->loadXml('
            <locales>
                <locale>translation.ini</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('', $locales[0]->getName());
    }

    /**
     * @throws ConfigurationException
     */
    public function testOtherNodesAreSkipped(): void
    {
        $xmlNode = $this->loadXml('
            <locales>
                <other></other>
                <locale>translation.ini</locale>
            </locales>
        ');

        $locales = $this->loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(1, $locales);
    }
}
