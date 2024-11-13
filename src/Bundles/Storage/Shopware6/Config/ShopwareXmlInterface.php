<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Config;

use PHPUnuhi\Models\Translation\Translation;

interface ShopwareXmlInterface
{
    /**
     * @return array<mixed>
     */
    public function readTranslations(string $locale): array;

    /**
     * @param Translation[] $translations
     */
    public function writeTranslations(string $locale, array $translations): void;
}
