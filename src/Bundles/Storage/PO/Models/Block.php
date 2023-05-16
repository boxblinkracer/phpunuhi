<?php

namespace PHPUnuhi\Bundles\Storage\PO\Models;

use PHPUnuhi\Traits\StringTrait;

class Block
{
    use StringTrait;

    /**
     * @var array<string>
     */
    private $lines;


    /**
     * @param array<string> $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return array<string>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        foreach ($this->lines as $line) {

            $isId = $this->stringDoesStartsWith($line, 'msgid');

            if ($isId) {
                return $this->getLineValue($line);
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        foreach ($this->lines as $line) {

            $isId = $this->stringDoesStartsWith($line, 'msgstr');

            if ($isId) {
                return $this->getLineValue($line);
            }
        }

        return '';
    }

    /**
     * @param string $msg
     * @return void
     */
    public function setMessage(string $msg): void
    {
        foreach ($this->lines as &$line) {

            $isId = $this->stringDoesStartsWith($line, 'msgstr');

            if ($isId) {
                $line = 'msgstr "' . $msg . '"';
                return;
            }
        }
    }

    /**
     * @param string $line
     * @return string
     */
    private function getLineValue(string $line): string
    {
        $parts = explode(' ', $line, 2);

        $msg = $parts[1];

        return str_replace('"', '', $msg);
    }

}