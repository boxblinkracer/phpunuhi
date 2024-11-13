<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\JSON\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Models\Translation\Locale;

class JsonStorageTest extends TestCase
{
    private Locale $locale;

    /**
     * @var array<string>
     */
    private array $files = [];



    protected function setUp(): void
    {
        parent::setUp();

        $locale = new Locale('nl-NL', false, 'nl-NL.json', '');
        $locale->addTranslation('nes.ted', 'value', '');
        $locale->addTranslation('foo', 'bar', '');

        $this->locale = $locale;
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        \array_map('unlink', \array_filter($this->files, 'is_file'));
    }


    public function testIndentBy4WithNewLineAtTheEnd(): void
    {
        $testFile = $this->createRandomFile();
        $storage = new JsonSaver(4, false, true);

        $hierarchy = new StorageHierarchy(true, '.');

        static::assertSame(2, $storage->saveLocale($this->locale, $hierarchy, $testFile));
        $json = \file_get_contents($testFile);
        $expected = <<<'JSON'
{
    "nes": {
        "ted": "value"
    },
    "foo": "bar"
}

JSON;

        static::assertSame($expected, $json);
    }


    public function testIndentBy2WithoutNewLineAtTheEnd(): void
    {
        $testFile = $this->createRandomFile();
        $storage = new JsonSaver(2, false, false);

        $hierarchy = new StorageHierarchy(true, '.');

        static::assertSame(2, $storage->saveLocale($this->locale, $hierarchy, $testFile));
        $json = \file_get_contents($testFile);
        $expected = <<<'JSON'
{
  "nes": {
    "ted": "value"
  },
  "foo": "bar"
}
JSON;

        static::assertSame($expected, $json);
    }



    public function testIndentBy5AndSortedKeys(): void
    {
        $testFile = $this->createRandomFile();
        $storage = new JsonSaver(5, true, false);

        $hierarchy = new StorageHierarchy(true, '.');

        static::assertSame(2, $storage->saveLocale($this->locale, $hierarchy, $testFile));
        $json = \file_get_contents($testFile);
        $expected = <<<'JSON'
{
     "foo": "bar",
     "nes": {
          "ted": "value"
     }
}
JSON;

        static::assertSame($expected, $json);
    }


    private function createRandomFile(): string
    {
        $result = (string)\tempnam(\sys_get_temp_dir(), 'phpunuhiJsonStorageTest');

        $this->files[] = $result;

        return $result;
    }
}
