<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Locale;

class TranslationFile
{
    private CommandPrompt $commandPrompt;

    public function __construct(CommandPrompt $commandPrompt)
    {
        $this->commandPrompt = $commandPrompt;
    }

    public function autoCreate(Configuration $configuration): void
    {
        foreach ($configuration->getTranslationSets() as $translationSet) {
            foreach ($translationSet->getLocales() as $locale) {
                $this->ensureExists($locale);
            }
        }
    }

    private function ensureExists(Locale $locale): void
    {
        if (file_exists($locale->getFilename())) {
            return;
        }

        $question =
            "Not found: <comment>{$locale->getFilename()}</comment>" . PHP_EOL .
            ' Should be generated?';

        if ($this->commandPrompt->askYesNoQuestion($question) === false) {
            return;
        }

        $this->createFile($locale->getFilename());
    }

    private function createFile(string $filename): void
    {
        $basedir = dirname($filename);

        if (is_dir($basedir) === false) {
            mkdir($basedir, 0755, true);
        };

        $content = StorageFactory::getInstance()->getStorageFileTemplate($filename);

        file_put_contents($filename, $content);
    }
}
