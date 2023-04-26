<?php

namespace phpunit\Services\Placeholder;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class PlaceholderEncoderTest extends TestCase
{

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

        $this->assertEquals('Hello, my name is [/' . $p1->getId() . '[/ [/' . $p2->getId() . '[/! Thank you for your [/' . $p3->getId() . '[/', $encodedString);
    }

    /**
     *
     * @return void
     */
    public function testDecode()
    {
        # marker placeholder
        $p1 = new Placeholder("{firstname}");
        $p2 = new Placeholder("{lastname}");
        # term
        $p3 = new Placeholder("attention");

        $placeholders = [$p1, $p2, $p3];

        $text = 'Hello, my name is [/' . $p1->getId() . '[/ [/' . $p2->getId() . '[/! Thank you for your [/' . $p3->getId() . '[/';

        $encoder = new PlaceholderEncoder();
        $encodedString = $encoder->decode($text, $placeholders);

        $this->assertEquals('Hello, my name is {firstname} {lastname}! Thank you for your attention', $encodedString);
    }

}