<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ImportInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class HTMLImporter implements ImportInterface
{

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     */
    function import(TranslationSet $set, string $filename): ImportResult
    {
        return new ImportResult(0, 0);
    }


}