<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ValidatorInterface
{
    public function getTypeIdentifier(): string;


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult;
}
