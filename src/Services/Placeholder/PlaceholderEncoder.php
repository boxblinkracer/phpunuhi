<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Placeholder;

class PlaceholderEncoder
{
    public const ENCODING_MARKER = '//';


    /**
     * @param Placeholder[] $placeholders
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
     * @param Placeholder[] $placeholders
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
