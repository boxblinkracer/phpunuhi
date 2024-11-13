<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\CaseStyle;

use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;

class CaseStyleConverterFactory
{
    /**
     * @var CaseStyleConverterInterface[]
     */
    private array $converters = [];



    public function __construct()
    {
        $this->converters[] = new UpperCaseConverter();
        $this->converters[] = new LowerCaseConverter();
        $this->converters[] = new CamelCaseConverter();
    }

    /**
     * @throws CaseStyleNotFoundException
     */
    public function fromIdentifier(string $identifier): CaseStyleConverterInterface
    {
        foreach ($this->converters as $converter) {
            if ($converter->getIdentifier() === $identifier) {
                return $converter;
            }
        }

        throw new CaseStyleNotFoundException('No CaseStyle converter found for identifier: ' . $identifier);
    }
}
