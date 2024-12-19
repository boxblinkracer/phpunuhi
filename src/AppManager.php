<?php

declare(strict_types=1);

namespace PHPUnuhi;

use Exception;
use PHPUnuhi\Commands\AvailableServicesCommand;
use PHPUnuhi\Commands\ExportCommand;
use PHPUnuhi\Commands\FixMessCommand;
use PHPUnuhi\Commands\FixSpellingCommand;
use PHPUnuhi\Commands\FixStructureCommand;
use PHPUnuhi\Commands\ImportCommand;
use PHPUnuhi\Commands\ListTranslationKeysCommand;
use PHPUnuhi\Commands\ListTranslationsCommand;
use PHPUnuhi\Commands\MigrateCommand;
use PHPUnuhi\Commands\ScanUsageCommand;
use PHPUnuhi\Commands\StatusCommand;
use PHPUnuhi\Commands\TranslateCommand;
use PHPUnuhi\Commands\ValidateAllCommand;
use PHPUnuhi\Commands\ValidateCommand;
use PHPUnuhi\Commands\ValidateCoverageCommand;
use PHPUnuhi\Commands\ValidateMessCommand;
use PHPUnuhi\Commands\ValidateSimilarityCommand;
use PHPUnuhi\Commands\ValidateSpellingCommand;
use PHPUnuhi\Commands\ValidateStructureCommand;
use PHPUnuhi\Exceptions\ConfigurationException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class AppManager
{
    /**
     * @var array<Command> $extensionCommands
     */
    private static array $extensionCommands = [];


    /**
     * @throws Exception
     */
    public static function run(): void
    {
        // Check if configuration argument is provided
        global $argv;
        $configPath = self::getConfigurationPath($argv);

        // Load bootstrap if specified in the configuration
        if ($configPath !== null) {
            self::loadBootstrap($configPath);
        }

        $application = new Application('PHPUnuhi', PHPUnuhi::getVersion());

        // Register commands
        $application->addCommands(self::getDefaultCommands());

        foreach (self::$extensionCommands as $command) {
            $application->add($command);
        }

        $application->setDefaultCommand('list');
        $application->run();
    }

    /**
     * @return Command[]
     */
    private static function getDefaultCommands(): array
    {
        return [
            new AvailableServicesCommand(),
            new StatusCommand(),
            new ListTranslationKeysCommand(),
            new ListTranslationsCommand(),
            new ValidateCommand(),
            new ValidateAllCommand(),
            new ValidateMessCommand(),
            new ValidateCoverageCommand(),
            new ValidateStructureCommand(),
            new ValidateSpellingCommand(),
            new ValidateSimilarityCommand(),
            new ImportCommand(),
            new ExportCommand(),
            new TranslateCommand(),
            new MigrateCommand(),
            new FixStructureCommand(),
            new FixMessCommand(),
            new ScanUsageCommand(),
            new FixSpellingCommand(),
        ];
    }


    public static function registerExtensionCommand(Command $command): void
    {
        self::$extensionCommands[$command->getName()] = $command;
    }


    /**
     * @param array<mixed> $args
     */
    private static function getConfigurationPath(array $args): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--configuration=')) {
                return str_replace('--configuration=', '', $arg);
            }
        }
        return null;
    }

    /**
     * Loads the bootstrap file if specified in the configuration XML.
     *
     * @throws Exception
     */
    private static function loadBootstrap(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new Exception("Configuration file not found: $configPath");
        }

        $xml = simplexml_load_string((string)file_get_contents($configPath));
        if (!$xml) {
            throw new Exception("Invalid XML in configuration file: $configPath");
        }

        $rootConfigDir = dirname($configPath) . '/';

        $bootstrapFile = (string)($xml->attributes()->bootstrap ?? '');

        if ($bootstrapFile === '') {
            return;
        }

        $bootstrapFixedFilename = (string)realpath($rootConfigDir . '/' . $bootstrapFile);

        if (file_exists($bootstrapFixedFilename)) {
            require_once $bootstrapFixedFilename;
        } elseif (!file_exists($bootstrapFixedFilename)) {
            throw new ConfigurationException('Bootstrap file not found: ' . $bootstrapFixedFilename);
        }
    }
}
