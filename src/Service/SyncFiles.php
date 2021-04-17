<?php

namespace MusicSync\Service;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\FileOperation\FsObject;

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
        // @todo Just demo code for now
        // @todo Give all objects in the struct the same parent folder
        // @todo Why is the recursion not working?
        echo "\n";
        foreach ($this->iterator($this->sourceDirectory) as $fsObject) {
            /* @var $fsObject FsObject */
            $type = str_replace(
                ['MusicSync\\Service\\', 'MusicSync\\Test\\', ],
                '',
                get_class($fsObject));
            echo $type . ' : path=' . $fsObject->getPath() . '; name=' . $fsObject->getName() . "\n";
        }
    }

    /**
     * I am pondering here using a generator and yielding each item. This will
     * allow me to recursively explore two directories at the same time, stopping
     * one of them if the other one has extra objects.
     *
     * @todo Rewrite these comments when we're done
     * @todo Assess whether the end solution needs $level
     */
    public function iterator(Directory $directory, $level = 0)
    {
        foreach ($directory->getContents() as $fsObject) {
            /* @var $fsObject FsObject */
            yield $fsObject;
            if ($fsObject instanceof Directory) {
                yield from $this->iterator($fsObject, $level + 1);
            }
        }
    }
}
