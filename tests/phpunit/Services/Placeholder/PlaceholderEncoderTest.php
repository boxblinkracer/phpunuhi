<?php

namespace phpunit\Services\Placeholder;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class PlaceholderEncoderTest extends TestCase
{

    private const MARKER = '//';


    /**
     * This test verifies that our encoding marker is not touched.
     * Some translators accidentally start conversion, but the current one
     * works really good and nothing is touched.
     *
     * @return void
     */
    public function testEncodingMarker()
    {
        $this->assertEquals('//', PlaceholderEncoder::ENCODING_MARKER);
    }

    /**
     *
     * @return void
     */
    public function testEncode()
    {
        $text = 'Hello, my name is {firstname} {lastname}! Thank you for your attention';

        # marker placeholder
        $p1 = new Placeholder("{firstname}");
        $p2 = new Placeholder("{lastname}");
        # term
        $p3 = new Placeholder("attention");

        $placeholders = [$p1, $p2, $p3];

        $encoder = new PlaceholderEncoder();
        $encodedString = $encoder->encode($text, $placeholders);

        $this->assertEquals('Hello, my name is ' . self::MARKER . $p1->getId() . self::MARKER . ' ' . self::MARKER . $p2->getId() . self::MARKER . '! Thank you for your ' . self::MARKER . $p3->getId() . self::MARKER, $encodedString);
    }

    /**
     *
     * @return void
     */
    public function testDecode()
    {
        # marker placeholderø
        $p1 = new Placeholder("{firstname}");
        $p2 = new Placeholder("{lastname}");
        # term
        $p3 = new Placeholder("attention");

        $placeholders = [$p1, $p2, $p3];

        $text = 'Hello, my name is ' . self::MARKER . $p1->getId() . self::MARKER . ' ' . self::MARKER . $p2->getId() . self::MARKER . '! Thank you for your ' . self::MARKER . $p3->getId() . self::MARKER;

        $encoder = new PlaceholderEncoder();
        $encodedString = $encoder->decode($text, $placeholders);

        $this->assertEquals('Hello, my name is {firstname} {lastname}! Thank you for your attention', $encodedString);
    }

}