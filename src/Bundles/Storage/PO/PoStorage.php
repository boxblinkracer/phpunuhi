<?php

namespace PHPUnuhi\Bundles\Storage\PO;

use PHPUnuhi\Bundles\Storage\PO\Models\Block;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class PoStorage implements StorageInterface
{

    use StringTrait;


    /**
     * @var bool
     */
    private $eolLast;


    /**
     * @return string
     */
    public function getStorageName(): string
    {
        return 'po';
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'po';
    }

    /**
     * @return bool
     */
    public function supportsFilters(): bool
    {
        return false;
    }

    /**
     * @return StorageHierarchy
     */
    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            false,
            ''
        );
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function configureStorage(TranslationSet $set): void
    {
        $this->eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param TranslationSet $set
     * @return void
     * @throws \Exception
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {

            $lines = $this->getLines($locale->getFilename());
            $blocks = $this->getBlocks($lines);

            foreach ($blocks as $block) {

                $id = $block->getId();
                $value = $block->getMessage();

                $locale->addTranslation($id, $value, '');
            }
        }
    }


    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;

        $contentBuffer = [];

        foreach ($set->getLocales() as $locale) {
            $localeCount++;
            $translationCount += $this->saveLocale($locale, $contentBuffer, $locale->getFilename());
        }

        foreach ($contentBuffer as $filename => $content) {
            file_put_contents($filename, $content);
        }

        return new StorageSaveResult($localeCount, $translationCount);

    }

    /**
     * @param Locale $locale
     * @param string $filename
     * @return StorageSaveResult
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $contentBuffer = [];

        $translationCount = $this->saveLocale($locale, $contentBuffer, $filename);

        file_put_contents($filename, $contentBuffer);

        return new StorageSaveResult(1, $translationCount);
    }

    /**
     * @param Locale $locale
     * @param array<mixed> $contentBuffer
     * @param string $filename
     * @return int
     */
    private function saveLocale(Locale $locale, array &$contentBuffer, string $filename): int
    {
        $lines = $this->getLines($locale->getFilename());
        $blocks = $this->getBlocks($lines);

        $existingKeys = [];
        $newLines = [];

        foreach ($blocks as $block) {

            $id = $block->getId();

            $existingKeys[] = $id;

            foreach ($locale->getTranslations() as $translation) {
                if ($translation->getID() === $id) {
                    $block->setMessage($translation->getValue());

                    foreach ($block->getLines() as $line) {
                        $newLines[] = $line;
                    }
                    $newLines[] = '';
                    break;
                }
            }

            $newLines[] = '';
        }

        $newLines[] = '';

        foreach ($locale->getTranslations() as $translation) {

            $found = false;

            foreach ($existingKeys as $existingKey) {
                if ($existingKey === $translation->getID()) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newLines[] = '';
                $newLines[] = 'msgid "' . $translation->getID() . '"';
                $newLines[] = 'msgstr "' . $translation->getValue() . '"' . PHP_EOL;
            }
        }


        $newLines = $this->clearLineBreaks($newLines);

        $content = implode(PHP_EOL, $newLines);

        # last EOL is optional, so let's remove it first
        $content = rtrim($content, PHP_EOL);

        if ($this->eolLast) {
            $content .= PHP_EOL;
        }

        $contentBuffer[$filename] = $content;

        return 0;
    }

    /**
     * @param array<string> $lines
     * @return array<string>
     */
    private function clearLineBreaks(array $lines): array
    {
        $newLines = [];
        $count = 0;
        foreach ($lines as $line) {

            if (empty($line)) {
                if ($count <= 0) {
                    $newLines[] = $line;
                    $count += 1;
                }

            } else {
                $newLines[] = $line;
                $count = 0;
            }
        }
        return $newLines;
    }

    /**
     * @param string $filename
     * @return array<string>
     */
    private function getLines(string $filename): array
    {
        $lines = [];

        $handle = fopen($filename, 'rb');

        if (!$handle) {
            return $lines;
        }

        while (($line = fgets($handle)) !== false) {

            $line = trim($line);
            $lines[] = $line;
        }

        fclose($handle);

        return $lines;
    }

    /**
     * @param array<string> $lines
     * @return array<Block>
     */
    private function getBlocks(array $lines): array
    {
        $blocks = [];
        $inBlock = false;

        $currentBlockLines = [];

        foreach ($lines as $line) {

            if (!empty($line)) {

                if (!$inBlock) {
                    # start new block
                    $inBlock = true;
                    $currentBlockLines = [];
                }
            }

            if (empty($line)) {
                if (!empty($currentBlockLines)) {
                    $blocks[] = new Block($currentBlockLines);
                }
                $currentBlockLines = [];
            } else {

                $currentBlockLines[] = $line;
            }
        }

        if (!empty($currentBlockLines)) {
            $blocks[] = new Block($currentBlockLines);
        }

        return $blocks;
    }
}
