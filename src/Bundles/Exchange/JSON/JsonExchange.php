<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange\JSON;

use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\ImportEntry;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\GroupName\GroupNameService;
use PHPUnuhi\Traits\StringTrait;

class JsonExchange implements ExchangeInterface
{
    use StringTrait;


    public function getName(): string
    {
        return 'json';
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return [];
    }

    /**
     * @param array<mixed> $options
     */
    public function setOptionValues(array $options): void
    {
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
        $data = [];

        $groupNameService = new GroupNameService();

        foreach ($set->getAllTranslationIDs() as $id) {
            $entry = [];
            $entry['id'] = $id;

            foreach ($set->getLocales() as $locale) {
                $translation = $locale->findTranslation($id);
                $entry['group'] = $groupNameService->getPropertyName($translation->getGroup());
                $entry['key'] = $translation->getKey();
                $entry[$locale->getName()] = $translation->getValue();
            }

            $data[] = $entry;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($outputDir . '/' . $set->getName() . '.json', $json);
    }


    public function import(string $filename): ImportResult
    {
        $entries = [];

        $json = file_get_contents($filename);

        $data = json_decode((string)$json, true);

        $groupNameService = new GroupNameService();

        foreach ($data as $entry) {
            $id = $entry['id'];
            $key = $groupNameService->getPropertyName($id);
            $group = $groupNameService->getGroupID($id);

            foreach ($entry as $locale => $value) {
                if ($locale === 'id') {
                    continue;
                }

                $entries[] = new ImportEntry($locale, $key, $group, $value);
            }
        }

        return new ImportResult($entries);
    }
}
