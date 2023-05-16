<?php

namespace PHPUnuhi\Bundles\Storage\PO;

use PHPUnuhi\Bundles\Storage\PO\Models\Block;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class PoStorage implements StorageInterface
{

    use StringTrait;

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
     * @throws \Exception
     */
    public function loadTranslations(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {

            $lines = $this->getLines($locale->getFilename());
            $blocks = $this->getBlocks($lines);

            /** @var Block $block */
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
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;

        $fileContents = [];

        foreach ($set->getLocales() as $locale) {

            $lines = $this->getLines($locale->getFilename());
            $blocks = $this->getBlocks($lines);

            $existingKeys = [];
            $newLines = [];

            /** @var Block $block */
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
                    $newLines[] = 'msgstr "' . $translation->getValue() . '"';
                    $newLines[] = '';
                }
            }

            $newLines = $this->clearLineBreaks($newLines);

            $fileContents[$locale->getFilename()] = implode(PHP_EOL, $newLines);
        }

        foreach ($fileContents as $filename => $content) {
            file_put_contents($filename, $content);
        }

        return new StorageSaveResult(0, 0);
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
