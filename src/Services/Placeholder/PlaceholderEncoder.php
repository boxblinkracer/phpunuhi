<?php

namespace PHPUnuhi\Services\Placeholder;

class PlaceholderEncoder
{

    /**
     *
     */
    public const ENCODING_MARKER = '//';


    /**
     * @param string $text
     * @param Placeholder[] $placeholders
     * @return string
     */
    public function encode(string $text, array $placeholders): string
    {
        $tmpValue = $text;

        foreach ($placeholders as $placeholder) {
            $tmpValue = str_replace(
                $placeholder->getValue(),
                self::ENCODING_MARKER . $placeholder->getId() . self::ENCODING_MARKER,
                $tmpValue
            );
        }

        return $tmpValue;
    }

    /**
     * @param string $text
     * @param Placeholder[] $placeholders
     * @return string
     */
    public function decode(string $text, array $placeholders): string
    {
        $tmpValue = $text;

        foreach ($placeholders as $placeholder) {
            $tmpValue = str_replace(
                self::ENCODING_MARKER . $placeholder->getId() . self::ENCODING_MARKER,
                $placeholder->getValue(),
                $tmpValue
            );
        }

        return $tmpValue;
    }
}