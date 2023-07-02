<?php

namespace PHPUnuhi\Commands\Core;


use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Services\GroupName\GroupNameService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixStructureCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('fix:structure')
            ->setDescription('Fixes the structure of your translation sets.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '');

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

        $io->title('PHPUnuhi Fix Structure');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $setName = $this->getConfigStringValue('set', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $countCreated = 0;

        $groupNameService = new GroupNameService();

        foreach ($config->getTranslationSets() as $set) {

            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Fixing Translation Set: ' . $set->getName());

            foreach ($set->getAllTranslationIDs() as $currentID) {

                foreach ($set->getLocales() as $locale) {
                    try {
                        $locale->findTranslation($currentID);
                    } catch (TranslationNotFoundException $ex) {

                        $groupName = $groupNameService->getGroupID($currentID);
                        $propertyKey = $groupNameService->getPropertyName($currentID);
                        $io->writeln('   [+] create translation: [' . $locale->getName() . '] ' . $currentID);

                        # if we have no separate group ID, then do NOT create it!
                        # this is for storage formats without group
                        $groupName = ($groupName === $propertyKey) ? '' : $groupName;

                        $locale->addTranslation(
                            $propertyKey,
                            '',
                            $groupName
                        );

                        $countCreated++;
                    }
                }
            }

            $io->block('saving translations of this set...');

            $storageSaver = StorageFactory::getInstance()->getStorage($set);

            $storageSaver->saveTranslationSet($set);
        }

        $io->success($countCreated . ' translations have been created!');
        exit(0);
    }

}
