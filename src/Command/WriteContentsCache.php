<?php

namespace MusicSync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WriteContentsCache extends Command
{
    protected function configure()
    {
        $this
            ->setName('cache:create')
            ->setDescription('Writes a cache of a directory structure to a file to speed up future operations');

        // Mandatory options
        /*
        $this->addOption(
            'name',
            'a',
            InputOption::VALUE_REQUIRED,
            'A unique name by which this path is known (e.g. personal-laptop)'
        );
        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'The path for this directory'
        );
        */

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'A unique name by which this path is known (e.g. personal-laptop)',
        );
        $this->addArgument(
            'path',
            InputArgument::REQUIRED,
            'The path for this directory'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return self::SUCCESS;
    }
}
