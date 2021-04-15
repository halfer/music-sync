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

    /**
     * Determines if the action is to be carried out or just described
     *
     * @param bool $dryRun
     */
    public function setDryRun(bool $dryRun)
    {
        // @todo
    }

    /**
     * Determines if files can be deleted on the destination
     *
     * @param bool $deleteDest
     */
    public function setDeleteDestinationFiles(bool $deleteDest)
    {
        // @todo
    }

    /**
     * Determines if deletions use a console prompt
     *
     * @param bool $noInteractive
     */
    public function setNoInteractiveDelete(bool $noInteractive)
    {
        // @todo
    }

    /**
     * Main service entrypoint
     */
    public function sync()
    {
        // @todo
    }
}
