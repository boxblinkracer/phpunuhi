<?php

use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CustomCommand extends Command
{
    use CommandTrait;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('custom:command-xy')
            ->setDescription('Custom Command')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PHPUnuhi Custom Command XY');

        return 0;
    }
}
