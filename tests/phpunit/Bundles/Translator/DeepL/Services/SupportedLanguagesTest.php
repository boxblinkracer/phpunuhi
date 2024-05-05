<?php

namespace phpunit\Bundles\Translator\DeepL\Services;

use DeepL\DeepLException;
use DeepL\Language;
use DeepL\Translator;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\DeepL\Services\SupportedLanguages;
use ReflectionClass;
use stdClass;

class SupportedLanguagesTest extends TestCase
{
    /**
     * @dataProvider supportedLanguages
     * @param stdClass $data
     * @return void
     */
    public function testSupportedLocale(stdClass $data): void
    {
        //Arrange
        $translator = $this->createMock(Translator::class);
        $translator
            ->method('getTargetLanguages')
            ->willReturn([
                new Language('GERMAN', 'DE', true),
                new Language('FRANCE', 'FR', true),
                new Language('ENGLISH', 'EN-GB', true),
                new Language('ENGLISH', 'EN-US', true),
            ]);

        $service = new SupportedLanguages($translator);

        //Act
        $actual = $service->getAvailableLocale($data->locale);

        //Assert
        $this->assertEquals($data->expect, $actual);
    }

    /**
     * @return array<array<stdClass>>
     */
    public function supportedLanguages(): array
    {
        return [
            [(object)['locale' => 'de',    'expect' => 'de']],
            [(object)['locale' => 'de-AT', 'expect' => 'de']],
            [(object)['locale' => 'de-DE', 'expect' => 'de']],
            [(object)['locale' => 'DE-CH', 'expect' => 'de']],
            [(object)['locale' => 'fr-BE', 'expect' => 'fr']],
            [(object)['locale' => 'fr-CH', 'expect' => 'fr']],
            [(object)['locale' => 'fr',    'expect' => 'fr']],
            [(object)['locale' => 'en',    'expect' => 'en-gb']],
            [(object)['locale' => 'en-GB', 'expect' => 'en-gb']],
            [(object)['locale' => 'en-US', 'expect' => 'en-us']],
            [(object)['locale' => 'pt',    'expect' => 'pt-pt']],
        ];
    }

    public function testNotSupportedLocale(): void
    {
        //Arrange
        $translator = $this->createMock(Translator::class);
        $translator
            ->method('getTargetLanguages')
            ->willReturn([
                new Language('GERMAN', 'DE', true),
            ]);

        $service = new SupportedLanguages($translator);

        //Asser
        $this->expectException(DeepLException::class);

        //Act
        $service->getAvailableLocale('fr');
    }

    public function testCacheOneCallDeepLForLanguages(): void
    {
        //Arrange
        $translator = $this->createMock(Translator::class);
        $translator
            ->expects($this->once()) //Assert
            ->method('getTargetLanguages')
            ->willReturn([
                new Language('GERMAN', 'DE', true),
            ]);

        $service = new SupportedLanguages($translator);

        //Act
        $service->getAvailableLocale('de');
        $service->getAvailableLocale('de');
    }

    protected function tearDown(): void
    {
        $refClass = new ReflectionClass(SupportedLanguages::class);

        $refProperty = $refClass->getProperty('supportedLanguages');
        $refProperty->setAccessible(true);
        $refProperty->setValue(null);
        $refProperty->setAccessible(false);
    }
}
