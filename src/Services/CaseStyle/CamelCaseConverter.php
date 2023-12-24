<?php

namespace PHPUnuhi\Services\CaseStyle;

use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

class CamelCaseConverter
{

    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string
    {
        $validator = new CamelCaseValidator();

        if ($validator->isValid($text)) {
            return $text;
        }

        $text = ucwords(str_replace(['-', '_'], ' ', $text));

        $parts = explode(' ', $text);

        $final = '';
        $first = true;

        foreach ($parts as &$part) {
            if ($first) {
                $final .= lcfirst(strtolower($part));
                $first = false;
            } else {
                $final .= ucfirst(strtolower($part));
            }
        }

        return $final;
    }
}
