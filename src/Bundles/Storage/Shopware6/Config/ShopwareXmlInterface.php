<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Config;

use PHPUnuhi\Models\Translation\Translation;

interface ShopwareXmlInterface
{

    /**
     * @param string $locale
     * @return array<mixed>
     */
    public function readTranslations(string $locale): array;

    /**
     * @param string $locale
     * @param Translation[] $translations
     * @return void
     */
    public function writeTranslations(string $locale, array $translations): void;
}
