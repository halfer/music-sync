<?php

namespace MusicSync\Service;

use Generator;
use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\FileOperation\FsObject;

class SyncFiles
{
    protected FileOperationFactory $factory;
    protected Directory $sourceDirectory;
    protected Directory $destinationDirectory;
    protected array $operations;

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
     *
     * @todo Just demo code for now
     */
    public function sync()
    {
        $this->clearOperations();

        // Let's create two iterators we can compare
        $sourceList = $this->iterator($this->sourceDirectory);
        $destList = $this->iterator($this->destinationDirectory);

        /* @var $source FsObject */
        /* @var $dest FsObject */

        while ($sourceList->valid() || $destList->valid()) {

            $source = $sourceList->current();
            $dest = $destList->current();

            if ($source && $dest) {
                $this->caseBothExist(
                    $sourceList, $destList,
                    $source, $dest
                );
            } elseif ($source || $dest) {
                echo "Should not happen yet\n";
            } else {
                echo "Finish condition\n";
                break;
            }
        }

        return $this;
    }

    protected function caseBothExist(
        Generator $sourceList, Generator $destList,
        FsObject $source, FsObject $dest)
    {
        // Get sizes
        $sourceSize = $this->getObjectSize($source);
        $destSize = $this->getObjectSize($dest);

        $sameLevel = true; // $source->getLevel() === $dest->getLevel();
        $sameName = $source->getName() === $dest->getName();
        $sameSize = $sourceSize === $destSize;

        if ($sameLevel && $sameName) {
            if ($sameSize) {
                $this->caseFilesIdentical($source, $dest);
            } else {
                $this->caseFilesDiffer($source, $dest);
            }
            $sourceList->next();
            $destList->next();
        } elseif ($sameLevel) {
            $this->caseFileMissing(
                $sourceList, $destList,
                $source, $dest
            );
        } else {
            // New unexpected level
            echo "New level detected\n";
        }
    }

    protected function caseFilesIdentical(FsObject $source, FsObject $dest)
    {
        $this->pushOperation(
            'noop',
            "{$source->getName()} and {$dest->getName()} identical"
        );
    }

    protected function caseFilesDiffer(FsObject $source, FsObject $dest)
    {
        echo "Copy, size difference\n";
    }

    protected function caseFileMissing(
        Generator $sourceList, Generator $destList,
        FsObject $source, FsObject $dest)
    {
        echo $source->getName() . ' ' . $dest->getName() . "\n";
        if ($source->getName() < $dest->getName()) {
            echo "Missing dest object `{$source->getName()}`, need to add\n";
            $sourceList->next();
        } elseif ($source->getName() > $dest->getName()) {
            echo "Missing source object `{$dest->getName()}`, need to delete\n";
            $destList->next();
        }
    }

    protected function caseOneExists(
        Generator $sourceList, Generator $destList,
        ?FsObject $source, ?FsObject $dest)
    {

    }

    /**
     * @todo Remove this demo code?
     */
    protected function iterateOverSourceDir()
    {
        echo "\n";
        foreach ($this->iterator($this->sourceDirectory) as $fsObject) {
            /* @var $fsObject FsObject */
            $type = str_replace(
                ['MusicSync\\Service\\', 'MusicSync\\Test\\', ],
                '',
                get_class($fsObject));
            echo $type . ' : path=' . $fsObject->getPath() .
                '; name=' . $fsObject->getName() .
                "\n";
        }
    }

    protected function getObjectSize(FsObject $fsObject)
    {
        if ($fsObject instanceof Directory) {
            return 0;
        }

        return $fsObject->getSize();
    }

    /**
     * Recursive directory generator
     *
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

    public function getOperations(): array
    {
        return $this->operations;
    }

    protected function clearOperations()
    {
        $this->operations = [];
    }

    protected function pushOperation(string $type, string $details)
    {
        $this->operations[] = [
            'type' => $type,
            'details' => $details,
        ];
    }
}
