<?php

namespace PHPUnuhi\Components\Reporter;

use PHPUnuhi\Components\Reporter\JSON\JsonReporter;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Exceptions\ConfigurationException;

class ReporterFactory
{

    /**
     * @var ReporterFactory
     */
    private static $instance;

    /**
     * @var ReporterInterface[]
     */
    private $reporters;


    /**
     * @return ReporterFactory
     */
    public static function getInstance(): ReporterFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     */
    public function __construct()
    {
        $this->resetStorages();
    }


    /**
     * @return void
     */
    public function resetStorages(): void
    {
        $this->reporters = [];

        $this->reporters[] = new  JUnitReporter();
        $this->reporters[] = new  JsonReporter();
    }


    /**
     * @param string $name
     * @return ReporterInterface
     * @throws ConfigurationException
     */
    public function getReporter(string $name): ReporterInterface
    {
        if ($name === '' || $name === '0') {

            throw new \Exception('No name provided for the Reporter');
        }

        foreach ($this->reporters as $reporter) {

            if ($reporter->getName() === $name) {
                return $reporter;
            }
        }

        throw new ConfigurationException('No reporter found for name: ' . $name);
    }

}
