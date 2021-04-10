<?php

namespace MusicSync\Command;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\SyncFiles as SyncFilesService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFiles extends Base
{
    protected SyncFilesService $syncFilesService;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        // Create service ready to use
        $this->syncFilesService = new SyncFilesService(
            new FileOperationFactory()
        );
    }

    protected function configure()
    {
        $this
            ->setName('cache:sync')
            ->setDescription('Compares two caches and syncs files from one to the other');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getSyncFilesService()->sync();
        $output->writeln('Does nothing');

        return self::SUCCESS;
    }

    protected function getSyncFilesService(): SyncFilesService
    {
        return $this->syncFilesService;
    }
}
