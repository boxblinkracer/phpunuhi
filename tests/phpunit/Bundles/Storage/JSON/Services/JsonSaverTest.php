<?php

namespace PHPUnuhi\Tests\Bundles\Storage\JSON\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonSaver;
use PHPUnuhi\Models\Translation\Locale;

class JsonStorageTest extends TestCase
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var array<string>
     */
    private $files = [];


    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $locale = new Locale('nl-NL', false, 'nl-NL.json', '');
        $locale->addTranslation('nes.ted', 'value', '');
        $locale->addTranslation('foo', 'bar', '');

        $this->locale = $locale;
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        \array_map('unlink', \array_filter($this->files, 'is_file'));
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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


    /**
     * @return void
     */
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

    /**
     * @return string
     */
    private function createRandomFile(): string
    {
        $result = (string)\tempnam(\sys_get_temp_dir(), 'phpunuhiJsonStorageTest');

        $this->files[] = $result;

        return $result;
    }
}
