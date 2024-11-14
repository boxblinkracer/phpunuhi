<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Twig;

use Exception;
use PHPUnuhi\Bundles\MJML\MjmlScanner;
use PHPUnuhi\Exceptions\ConfigurationException;

class ScannerFactory
{
    private static ?\PHPUnuhi\Bundles\Twig\ScannerFactory $instance = null;

    /**
     * @var ScannerInterface[]
     */
    private array $scanners;



    public static function getInstance(): ScannerFactory
    {
        if (!self::$instance instanceof \PHPUnuhi\Bundles\Twig\ScannerFactory) {
            self::$instance = new self();
        }

        return self::$instance;
    }



    private function __construct()
    {
        $this->resetScanners();
    }

    /**
     * @throws ConfigurationException
     */
    public function registerScanner(ScannerInterface $scanner): void
    {
        $newName = $scanner->getScannerName();

        foreach ($this->scanners as $existingStorage) {
            if ($existingStorage->getScannerName() === $newName) {
                throw new ConfigurationException('Scanner with name already registered: ' . $newName);
            }
        }

        $this->scanners[] = $scanner;
    }


    /**
     * @return ScannerInterface[]
     */
    public function getScanners(): array
    {
        return $this->scanners;
    }

    /**
     * Resets the registered scanners to the default ones.
     */
    public function resetScanners(): void
    {
        $this->scanners = [];

        $this->scanners[] = new TwigScanner();
        $this->scanners[] = new MjmlScanner();
    }

    /**
     * @throws ConfigurationException
     */
    public function getScanner(string $name): ScannerInterface
    {
        if (trim($name) === '') {
            throw new Exception('No name provided for the Scanner');
        }

        foreach ($this->scanners as $scanner) {
            if ($scanner->getScannerName() === $name) {
                return $scanner;
            }
        }

        throw new ConfigurationException('No scanner found for name: ' . $name);
    }
}
