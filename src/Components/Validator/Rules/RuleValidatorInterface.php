<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Models\Translation\TranslationSet;

interface RuleValidatorInterface
{
    public function getRuleIdentifier(): string;


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult;
}
