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
     * @return void
     */
    protected function setUp(): void
    {
        $this->existingLocaleFile = __DIR__ . '/tmp_locale_file.json';
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
        $xml = '
            <locales basePath="./snippets">
                <locale name="en">' . $this->existingLocaleFile . '</locale>
                <locale name="de">' . $this->existingLocaleFile . '</locale>
            </locales>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new LocalesLoader();
        $locales = $loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(2, $locales);
        $this->assertEquals('en', $locales[0]->getName());
        $this->assertEquals('.' . $this->existingLocaleFile, $locales[0]->getFilename());
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
        $xml = '
            <locales>
                <locale name="en">./not-existing.json</locale>
            </locales>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new LocalesLoader();
        $locales = $loader->loadLocales($xmlNode, 'test.xml');

        $this->assertCount(1, $locales);
        $this->assertEquals('./not-existing.json', $locales[0]->getFilename());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testBasePathPlaceholder(): void
    {
        $xml = '
            <locales basePath="./snippets">
                <locale name="en">%base_path%/translation.json</locale>
            </locales>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new LocalesLoader();
        $locales = $loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('./snippets/translation.json', $locales[0]->getFilename());
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
        $xml = '
            <locales>
                <locale name="' . $locale . '">' . $placeholder . '/translation.json</locale>
            </locales>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new LocalesLoader();
        $locales = $loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('./' . $expectedLocalePart . '/translation.json', $locales[0]->getFilename());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testIniSectionLoaded(): void
    {
        $xml = '
            <locales>
                <locale name="en" iniSection="en-GB">%locale%/translation.ini</locale>
            </locales>
        ';

        $xmlNode = $this->loadXml($xml);

        $loader = new LocalesLoader();
        $locales = $loader->loadLocales($xmlNode, 'test.xml');

        $this->assertEquals('en-GB', $locales[0]->getIniSection());
    }
}
