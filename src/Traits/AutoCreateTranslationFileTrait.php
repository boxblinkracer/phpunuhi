<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

use PHPUnuhi\Configuration\Services\CommandPrompt;
use PHPUnuhi\Models\Translation\Locale;

trait AutoCreateTranslationFileTrait
{
    /**
     * @var null|string
     */
    private $storageFileTemplate;

    /**
     * @var CommandPrompt
     */
    protected $commandPrompt;

    public function setStorageFileTemplate(string $storageFileTemplate): self
    {
        $this->storageFileTemplate = $storageFileTemplate;
        return $this;
    }

    /**
     * @param Locale $locale
     * @return bool
     */
    protected function ensureTranslationFileExists(Locale $locale): bool
    {
        if (file_exists($locale->getFilename())) {
            return true;
        }

        $question =
            "Not Found: <comment>{$locale->getFilename()}</comment>" . PHP_EOL .
            ' Should be generated?';

        if ($this->commandPrompt->askYesNoQuestion($question) === false) {
            return false;
        }

        return $this->createFile($locale->getFilename()) !== false;
    }

    /**
     * @param string $filename
     * @return false|int
     */
    private function createFile($filename)
    {
        return file_put_contents($filename, $this->storageFileTemplate ?? '');
    }
}
