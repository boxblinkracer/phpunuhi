<?php

namespace PHPUnuhi\Components\Validator\CaseStyle;

use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\LowerCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\NumberCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\PascalCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\SnakeCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\StartCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\UpperCaseValidator;

class CaseStyleValidatorFactory
{

    /**
     * @var CaseStyleValidatorInterface[]
     */
    private $validators;


    /**
     *
     */
    public function __construct()
    {
        $this->validators = [];
        $this->validators[] = new KebabCaseValidator();
        $this->validators[] = new SnakeCaseValidator();
        $this->validators[] = new CamelCaseValidator();
        $this->validators[] = new StartCaseValidator();
        $this->validators[] = new LowerCaseValidator();
        $this->validators[] = new UpperCaseValidator();
        $this->validators[] = new PascalCaseValidator();
        $this->validators[] = new NumberCaseValidator();
    }

    /**
     * @param string $identifier
     * @return CaseStyleValidatorInterface
     * @throws CaseStyleNotFoundException
     */
    public function fromIdentifier(string $identifier): CaseStyleValidatorInterface
    {
        foreach ($this->validators as $validator) {

            if ($validator->getIdentifier() === $identifier) {
                return $validator;
            }
        }

        throw new CaseStyleNotFoundException('No CaseStyle validator found for identifier: ' . $identifier);
    }

}
