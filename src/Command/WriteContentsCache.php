<?php

namespace MusicSync\Command;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache as WriteContentsCacheService;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WriteContentsCache extends Base
{
    protected WriteContentsCacheService $writeContentsCacheService;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        // Create service ready to use
        $this->writeContentsCacheService = new WriteContentsCacheService(
            new FileOperationFactory()
        );
    }

    protected function configure()
    {
        $this
            ->setName('cache:create')
            ->setDescription('Writes a cache of a directory structure to a file to speed up future operations');

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

        $this->addOption(
            'cache-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'The path to a cache folder',

        );
    }


    /**
     * Runs the "WriteContentsCache" command
     *
     * @todo What happens if "path" contains rubbish?
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getWriteContentsCacheService();

        try {
            $this->checkForUnsupportedOptions($input);

            // Get args and validate what we can
            $name = $input->getArgument('name');
            $path = $input->getArgument('path');
            $service->validateName($name);

            // Populate the in-memory structure
            $service->create($path);

            // Serialise it and save it to disk
            $service->save($this->getCachePath(), $name);
            $return = self::SUCCESS;
        } catch (RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $return = self::FAILURE;
        }

        return $return;
    }

    /**
     * @todo This needs to be implemented & removed
     * @param InputInterface $input
     */
    protected function checkForUnsupportedOptions(InputInterface $input)
    {
        if ($input->getOption('cache-dir')) {
            throw new RuntimeException('--cache-dir is currently not supported');
        }
    }

    /**
     * Get the location of the cache
     *
     * @todo Allow this to be over-ridden by --cache-dir
     *
     * @return string
     */
    protected function getCachePath()
    {
        return $this->getHomeDir() . DIRECTORY_SEPARATOR . '.music-sync';
    }

    protected function getWriteContentsCacheService()
    {
        return $this->writeContentsCacheService;
    }
}
