<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Models\Translation\TranslationSet;

interface RuleValidatorInterface
{

    /**
     * @return string
     */
    public function getRuleIdentifier(): string;

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult;

}