<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter;

use InvalidArgumentException;
use PHPUnuhi\Components\Reporter\JSON\JsonReporter;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriter;
use PHPUnuhi\Services\Writers\File\FileWriter;
use PHPUnuhi\Services\Writers\Xml\XmlWriter;

class ReporterFactory
{
    private static ?\PHPUnuhi\Components\Reporter\ReporterFactory $instance = null;

    /**
     * @var ReporterInterface[]
     */
    private array $reporters;



    public static function getInstance(): ReporterFactory
    {
        if (!self::$instance instanceof \PHPUnuhi\Components\Reporter\ReporterFactory) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function __construct()
    {
        $this->resetStorages();
    }



    public function resetStorages(): void
    {
        $this->reporters = [];

        $dirWriter = new DirectoryWriter();

        $this->reporters[] = new JUnitReporter($dirWriter, new XmlWriter());
        $this->reporters[] = new JsonReporter($dirWriter, new FileWriter());
    }


    /**
     * @throws ConfigurationException
     */
    public function getReporter(string $name): ReporterInterface
    {
        if ($name === '' || $name === '0') {
            throw new InvalidArgumentException('No name provided for the Reporter');
        }

        foreach ($this->reporters as $reporter) {
            if ($reporter->getName() === $name) {
                return $reporter;
            }
        }

        throw new ConfigurationException('No reporter found for name: ' . $name);
    }
}
