<?php

namespace phpunit\Bundles\Storage\JSON\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonSaver;
use PHPUnuhi\Models\Translation\Locale;

class JsonStorageTest extends TestCase
{
    /**
     * @var Locale
     */
    private $locale;

    private $files = [];

    protected function setUp(): void
    {
        parent::setUp();

        $locale = new Locale('nl-NL', 'nl-NL.json', '');
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

        static::assertSame(2, $storage->saveLocale($this->locale, '.', $testFile));
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

        static::assertSame(2, $storage->saveLocale($this->locale, '.', $testFile));
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

        static::assertSame(2, $storage->saveLocale($this->locale, '.', $testFile));
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
        $result = \tempnam(\sys_get_temp_dir(), 'phpunuhiJsonStorageTest');

        $this->files[] = $result;

        return $result;
    }
}
