<?php

declare(strict_types=1);

namespace phpunit\Bundles\Storage\Shopware6;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\SnippetSet;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\SnippetSetFinderTrait;

class SnippetSetFinderTraitTest extends TestCase
{
    use SnippetSetFinderTrait;

    public function testFindByName(): void
    {
        $snippetSets = [
            new SnippetSet('1', 'BASE de-DE', 'de-DE'),
            new SnippetSet('2', 'BASE de-DE 2', 'de-DE'),
        ];

        $return = $this->findSnippetSet($snippetSets, 'BASE de-DE');
        self::assertEquals('1', $return->getId());
    }

    public function testFindByIso(): void
    {
        $snippetSets = [
            new SnippetSet('1', 'BASE de-DE', 'de-DE'),
            new SnippetSet('2', 'BASE en-GB', 'en-GB'),
        ];

        $return = $this->findSnippetSet($snippetSets, 'en-GB');
        self::assertEquals('2', $return->getId());
    }

    public function testNoSnippetSet(): void
    {
        $this->expectException(Exception::class);
        $this->findSnippetSet([], 'de-DE');
    }

    public function testNoMatchingSnippetSet(): void
    {
        $snippetSets = [
            new SnippetSet('1', 'BASE de-DE', 'de-DE'),
            new SnippetSet('2', 'BASE 2 de-DE', 'de-DE'),
        ];

        $this->expectException(Exception::class);
        $this->findSnippetSet($snippetSets, 'en-GB');
    }

    public function testExceptionOnMultipleMatchingNames(): void
    {
        $snippetSets = [
            new SnippetSet('1', 'BASE de-DE', 'de-DE'),
            new SnippetSet('2', 'BASE de-DE', 'de-DE'),
        ];

        $this->expectException(Exception::class);
        $this->findSnippetSet($snippetSets, 'BASE de-DE');
    }

    public function testExceptionOnMultipleMatchingIso(): void
    {
        $snippetSets = [
            new SnippetSet('1', 'BASE de-DE', 'de-DE'),
            new SnippetSet('2', 'BASE 2 de-DE', 'de-DE'),
        ];

        $this->expectException(Exception::class);
        $this->findSnippetSet($snippetSets, 'de-DE');
    }
}
