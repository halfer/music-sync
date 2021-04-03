<?php

namespace MusicSync\Command;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache as WriteContentsCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WriteContentsCache extends Command
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get args and see if they are acceptable
        $name = $input->getArgument('name');
        $path = $input->getArgument('path');
        $error = $this->validateArguments($name, $path);

        // @todo This is not a very nice error display - can we do better?
        if ($error) {
            $output->writeln('<error>Error: ' . $error . '</error>');
            return self::FAILURE;
        }

        $service = $this->getWriteContentsCacheService();
        $service->create($path);
        $service->save($this->getCachePath(), $name);

        // @todo We need to determine if everything went ok
        return self::SUCCESS;
    }

    protected function validateArguments(string $name, string $path)
    {
        // FIXME
        if (false) {
            $this->failInvalidName();
        }

        if (!$this->getWriteContentsCacheService()->dirExists($path)) {
            return 'No such folder';
        }

        return null;
    }

    protected function failInvalidName()
    {

    }

    /**
     * Get the location of the cache
     *
     * @todo Add the $HOME env var here, in a test-appropriate way
     * @todo Allow this to be over-ridden by --cache-dir
     *
     * @return string
     */
    protected function getCachePath()
    {
        return '/root/.music-sync';
    }

    protected function getWriteContentsCacheService()
    {
        return $this->writeContentsCacheService;
    }
}
