<?php

namespace phpunit\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Traits\XmlLoaderTrait;
use PHPUnuhi\Configuration\Services\LocalesLoader;
use PHPUnuhi\Exceptions\ConfigurationException;

class LocalesLoaderTest extends TestCase
{
    use XmlLoaderTrait;

    /**
     * @var string
     */
    private $existingLocaleFile;

    /**
     * @var LocalesLoader
     */
    private $loader;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->existingLocaleFile = __DIR__ . '/tmp_locale_file.json';

        $this->loader = new LocalesLoader();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (file_exists($this->existingLocaleFile)) {
            unlink($this->existingLocaleFile);
        }
    }

    /**
     * @throws ConfigurationException
     * @return void
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
     * @return void
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
     * @return void
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
     * @param string $placeholder
     * @throws ConfigurationException
     * @return void
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
     *
     * @param string $expectedLocalePart
     * @param string $placeholder
     * @param string $locale
     * @throws ConfigurationException
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
