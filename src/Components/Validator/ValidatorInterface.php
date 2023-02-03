<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ValidatorInterface
{

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return bool
     */
    public function validate(TranslationSet $set, StorageInterface $storage): bool;

}
