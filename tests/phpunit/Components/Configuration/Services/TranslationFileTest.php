<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\CommandPrompt;
use PHPUnuhi\Configuration\Services\TranslationFile;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class TranslationFileTest extends TestCase
{
    private CommandPrompt $commandPrompt;
    private Configuration $configuration;
    private Locale $locale;
    private TranslationSet $translationSet;
    private TranslationFile $translationFileSubject;

    protected function setUp(): void
    {
        $this->commandPrompt = $this->createMock(CommandPrompt::class);

        $this->locale = $this->createMock(Locale::class);

        $this->translationSet = $this->createMock(TranslationSet::class);
        $this->translationSet
            ->expects($this->once())
            ->method('getLocales')
            ->willReturn([$this->locale]);

        $this->configuration = $this->createMock(Configuration::class);
        $this->configuration
            ->expects($this->once())
            ->method('getTranslationSets')
            ->willReturn([
                $this->translationSet
            ]);

        $this->translationFileSubject = new TranslationFile($this->commandPrompt);
    }

    public function testNoCreateWithExistingFile(): void
    {
        //Arrange
        /** @phpstan-ignore method.notFound */
        $this->locale
            ->expects($this->once())
            ->method('getFilename')
            ->willReturn(__FILE__);

        //Assert
        /** @phpstan-ignore method.notFound */
        $this->commandPrompt
            ->expects($this->never())
            ->method('askYesNoQuestion');

        //Act
        $this->translationFileSubject->autoCreate($this->configuration);
    }

    public function testAutoCreateWithNonExistingFileAndUserDeclines(): void
    {
        //Arrange
        /** @phpstan-ignore method.notFound */
        $this->locale
            ->expects($this->any())
            ->method('getFilename')
            ->willReturn(__DIR__ . '/tranlastionset/de.ini');

        //Assert
        /** @phpstan-ignore method.notFound */
        $this->commandPrompt
            ->expects($this->once())
            ->method('askYesNoQuestion')
            ->willReturn(false);

        //Act
        $this->translationFileSubject->autoCreate($this->configuration);

        //Assert
        $this->assertFileDoesNotExist(__DIR__ . '/tranlastionset/de.ini');
    }

    public function testAutoCreateWithNonExistingFileAndUserAccepts(): void
    {
        //Arrange
        /** @phpstan-ignore method.notFound */
        $this->locale
            ->expects($this->any())
            ->method('getFilename')
            ->willReturn(__DIR__ . '/tranlastionset/de.ini');

        //Assert
        /** @phpstan-ignore method.notFound */
        $this->commandPrompt
            ->expects($this->once())
            ->method('askYesNoQuestion')
            ->willReturn(true);

        //Act
        $this->translationFileSubject->autoCreate($this->configuration);

        //Assert
        $this->assertFileExists(__DIR__ . '/tranlastionset/de.ini');
    }

    protected function tearDown(): void
    {
        @exec('rm -Rf ' . __DIR__ . '/tranlastionset/');
    }
}
