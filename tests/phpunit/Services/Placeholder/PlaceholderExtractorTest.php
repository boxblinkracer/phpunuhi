<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Placeholder;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Placeholder\PlaceholderExtractor;

class PlaceholderExtractorTest extends TestCase
{
    /**
     * This test verifies that both placeholders are correctly
     * extracted and returned from the given text.
     *
     */
    public function testBothPlaceholdersAreFound(): void
    {
        $text = 'Hello, my name is {firstname} {lastname}! Thank you for your attention';

        $extractor = new PlaceholderExtractor();

        $placeholders = $extractor->extract($text, '{', '}');

        $this->assertCount(2, $placeholders);

        $this->assertEquals('{firstname}', $placeholders[0]->getValue());
        $this->assertEquals('{lastname}', $placeholders[1]->getValue());
    }

    /**
     * This test verifies that we do get an empty list in case nothing is found.
     *
     */
    public function testNoPlaceholdersReturnsEmptyList(): void
    {
        $text = 'Hello! Thank you for your attention';

        $extractor = new PlaceholderExtractor();

        $placeholders = $extractor->extract($text, '{', '}');

        $this->assertCount(0, $placeholders);
    }

    /**
     * This test verifies that we only get the 1 correct placeholders.
     * The second and third one have missing start and end markers and must not be found.
     *
     */
    public function testMissingStartAndEndMarkers(): void
    {
        $text = 'Hello, my name is {firstname} lastname} {lastname';

        $extractor = new PlaceholderExtractor();

        $placeholders = $extractor->extract($text, '{', '}');

        $this->assertCount(1, $placeholders);

        $this->assertEquals('{firstname}', $placeholders[0]->getValue());
    }

    /**
     * This test verifies that we also get placeholders that contain a space in it.
     *
     */
    public function testPlaceholderWithSpace(): void
    {
        $text = 'Hello, my name {firstname lastname}';

        $extractor = new PlaceholderExtractor();

        $placeholders = $extractor->extract($text, '{', '}');

        $this->assertCount(1, $placeholders);

        $this->assertEquals('{firstname lastname}', $placeholders[0]->getValue());
    }
}
