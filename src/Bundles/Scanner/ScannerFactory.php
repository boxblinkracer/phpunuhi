<?php

namespace PHPUnuhi\Bundles\Twig;

use Exception;
use PHPUnuhi\Exceptions\ConfigurationException;

class ScannerFactory
{

    /**
     * @var ScannerFactory
     */
    private static $instance;

    /**
     * @var ScannerInterface[]
     */
    private $scanners;


    /**
     * @return ScannerFactory
     */
    public static function getInstance(): ScannerFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     *
     */
    private function __construct()
    {
        $this->resetScanners();
    }

    /**
     * @param ScannerInterface $scanner
     * @throws ConfigurationException
     * @return void
     */
    public function registerScanner(ScannerInterface $scanner): void
    {
        $newName = $scanner->getStorageName();

        foreach ($this->scanners as $existingStorage) {
            if ($existingStorage->getStorageName() === $newName) {
                throw new ConfigurationException('Scanner with name already registered: ' . $newName);
            }
        }

        $this->scanners[] = $scanner;
    }


    /**
     * Resets the registered scanners to the default ones.
     * @return void
     */
    public function resetScanners(): void
    {
        $this->scanners = [];

        $this->scanners[] = new TwigScanner();
    }

    /**
     * @param string $name
     * @throws ConfigurationException
     * @return ScannerInterface
     */
    public function getScanner(string $name): ScannerInterface
    {
        if (trim($name) === '') {
            throw new Exception('No name provided for the Scanner');
        }

        foreach ($this->scanners as $scanner) {
            if ($scanner->getStorageName() === $name) {
                return $scanner;
            }
        }

        throw new ConfigurationException('No scanner found for name: ' . $name);
    }
}
