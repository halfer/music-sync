<?php

namespace MusicSync\Service;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;

class SyncFiles
{
    protected FileOperationFactory $factory;
    protected Directory $sourceDirectory;
    protected Directory $destinationDirectory;

    public function __construct(FileOperationFactory $factory)
    {
        $this->factory = $factory;
    }

    public function setSourceDirectory(Directory $directory)
    {
        $this->sourceDirectory = $directory;

        return $this;
    }

    public function setDestinationDirectory(Directory $directory)
    {
        $this->destinationDirectory = $directory;

        return $this;
    }

    public function setDryRun(bool $dryRun)
    {
        // @todo
    }

    public function setDeleteDestinationFiles(bool $deleteDest)
    {
        // @todo
    }

    public function setNoInterativeDelete(bool $noInteractive)
    {
        // @todo
    }

    public function sync()
    {
        // @todo
    }
}
