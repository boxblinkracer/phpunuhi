<?php

namespace PHPUnuhi\Commands\Core;


use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Components\Validator\MixedStructureValidator;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates all your translations from your configuration')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $isAllValid = true;

        $validators = [];
        $validators[] = new CaseStyleValidator($output);
        $validators[] = new MixedStructureValidator($output);
        $validators[] = new EmptyContentValidator($output);

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation-Set: ' . $set->getName());


            $io->writeln('  -------------------------------------------------------------');
            $io->writeln('   Configuration for Translation-Set:');

            if (count($set->getCasingStyles()) > 0) {
                $styles = implode(', ', $set->getCasingStyles());
            } else {
                $styles = 'none';
            }

            $io->writeln('      [~] Case-styles: ' . $styles);
            $io->writeln('  -------------------------------------------------------------');
            $io->writeln('');
            $io->writeln("");


            $storage = StorageFactory::getStorage($set);

            $allValidatorsValid = true;

            foreach ($validators as $validator) {

                $isValid = $validator->validate($set, $storage);

                if (!$isValid) {
                    $allValidatorsValid = false;
                }
            }

            if ($allValidatorsValid) {
                $io->block('Translation-Set is valid!');
            } else {
                $io->note('Translation-Set is not valid!');
                $isAllValid = false;
            }
        }

        if ($isAllValid) {
            $io->success('All translations are valid!');
            exit(0);
        }

        $io->error('Not all translations are valid!');
        exit(1);
    }

}
