<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\Locale;

interface StorageLoaderInterface
{

    /**
     * @param Locale $locale
     * @return void
     */
    function loadTranslations(Locale $locale): void;

}
