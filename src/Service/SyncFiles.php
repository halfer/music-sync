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
                $this->caseBothExist($sourceList, $destList, $source, $dest);
            } elseif ($source || $dest) {
                $this->caseOneExists($sourceList, $destList, $source, $dest);
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
            throw new \RuntimeException(
                "FIXME New level detected, needs handler code?"
            );
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
        $this->pushOperation(
            'add',
            "Copy {$source->getName()} to dest"
        );
    }

    protected function caseFileMissing(
        Generator $sourceList, Generator $destList,
        FsObject $source, FsObject $dest)
    {
        if ($source->getName() < $dest->getName()) {
            $this->pushOperation(
                'add',
                "Copy {$source->getName()} to dest"
            );
            $sourceList->next();
        } elseif ($source->getName() > $dest->getName()) {
            $this->pushOperation(
                'del',
                "Delete {$dest->getName()} from dest"
            );
            $destList->next();
        }
    }

    /**
     * This caters for the case where one list is longer than the other
     *
     * @param Generator $sourceList
     * @param Generator $destList
     * @param FsObject|null $source
     * @param FsObject|null $dest
     */
    protected function caseOneExists(
        Generator $sourceList, Generator $destList,
        ?FsObject $source, ?FsObject $dest)
    {
        if ($source) {
            // Missing dest, so let's copy
            $this->pushOperation(
                'add',
                "Copy {$source->getName()} to dest"
            );
            $sourceList->next();
        } elseif ($dest) {
            // Missing source, so let's delete
            $this->pushOperation(
                'del',
                "Delete {$dest->getName()} from dest"
            );
            $destList->next();
        }
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
