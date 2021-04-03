<?php

namespace MusicSync\Service\FileOperation;

use MusicSync\Service\FileOperation\AcceptsFactory;
use RuntimeException;

class Directory extends FsObject
{
    use AcceptsFactory;

    const SORT_NAME = 'name';
    const SORT_SIZE = 'size';

    protected array $contents = [];
    protected bool $populated = false;
    protected bool $sortDirectionAscending = true;
    protected int $totalSize = 0;

    public function glob(string $pattern = '*')
    {
        $this->clearContents();
        $path = $this->getPath() .
            DIRECTORY_SEPARATOR .
            $this->getName() .
            DIRECTORY_SEPARATOR .
            $pattern;

        foreach (glob($path) as $path) {
            $this->pushObject($path);
        }
        $this->setPopulated();
    }

    /**
     * Populates FS structure, and optionally gets file sizes too
     *
     * @note should I track file and link sizes separately?
     *
     * @param bool $popSize
     */
    public function recursivePopulate(bool $popSize = false)
    {
        $sizeTotal = 0;

        $this->glob();
        foreach ($this->getContents() as $fsObject) {
            if ($fsObject instanceof Directory) {
                $fsObject->recursivePopulate($popSize);
                $sizeTotal += $fsObject->getTotalSize();
            }
            if ($popSize && $fsObject instanceof File) {
                $sizeTotal += $fsObject->populateSize();
            }
        }

        if ($popSize) {
            $this->setTotalSize($sizeTotal);
        }
    }

    protected function pushObject(string $path)
    {
        if (is_link($path)) {
            $this->contents[] = $this->getFactory()->createLink($path);
        } elseif (is_file($path)) {
            $this->contents[] = $this->getFactory()->createFile($path);
        } elseif (is_dir($path)) {
            $this->contents[] = $this->getFactory()->createDirectory($path);
        }
    }

    /**
     * @todo Use a custom exception here
     * @return array
     */
    public function getContents(): array
    {
        if (!$this->populated) {
            throw new RuntimeException('Cannot get contents before population');
        }

        return $this->contents;
    }

    /**
     * Creates a directory in the file system
     *
     * @return bool
     */
    public function create(): bool
    {
        $fullPath = $this->getPath() . DIRECTORY_SEPARATOR . $this->getName();
        $result = @mkdir($fullPath, 0600, true);

        return $result;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return is_dir(
            $this->getPath() . DIRECTORY_SEPARATOR . $this->getName()
        );
    }

    public function getFileCount()
    {
        return $this->countObjectsByType(File::class);
    }

    public function getDirCount()
    {
        return $this->countObjectsByType(Directory::class);
    }

    public function getLinkCount()
    {
        return $this->countObjectsByType(Link::class);
    }

    /**
     * Counts files across the whole structure
     *
     * @return int
     */
    public function getFileCountRecursive()
    {
        $totals = $this->countObjectsByTypeRecursively();

        return $totals[0];
    }

    /**
     * Counts symlinks across the whole structure
     *
     * @return int
     */
    public function getLinkCountRecursive()
    {
        $totals = $this->countObjectsByTypeRecursively();

        return $totals[1];
    }

    /**
     * Counts directories across the whole structure
     *
     * @return int
     */
    public function getDirCountRecursive()
    {
        $totals = $this->countObjectsByTypeRecursively();

        return $totals[2];
    }

    protected function countObjectsByTypeRecursively()
    {
        $totalFile = $totalLink = $totalDir = 0;

        foreach ($this->getContents() as $fsObject) {
            /* @var $fsObject FsObject */
            if ($fsObject instanceof Directory) {
                list($subTotalFile,
                     $subTotalLink,
                     $subTotalDir)= $fsObject->countObjectsByTypeRecursively();
                $totalFile += $subTotalFile;
                $totalLink += $subTotalLink;
                // Note that the dir we are scanning should also be counted :)
                $totalDir += $subTotalDir + 1;
            // Links count as files, so do links first :=)
            } elseif ($fsObject instanceof Link) {
                $totalLink += 1;
            } elseif ($fsObject instanceof File) {
                $totalFile += 1;
            }
        }

        return [$totalFile, $totalLink, $totalDir, ];
    }

    protected function countObjectsByType(string $type)
    {
        $count = 0;
        foreach ($this->contents as $fsObject)
        {
            if ($fsObject instanceof $type) {
                $count++;
            }
        }

        return $count;
    }

    public function sort(string $sortType, bool $ascending = true)
    {
        $this->setSortDirection($ascending);
        usort($this->contents, [$this, $this->getSorter($sortType)]);
    }

    public function recursiveSort(string $sortType, bool $ascending = true)
    {
        // Do a regular sort on self
        $this->sort($sortType, $ascending);

        // Now do the same for all children
        foreach ($this->getContents() as $fsObject) {
            if ($fsObject instanceof Directory) {
                $fsObject->recursiveSort($sortType, $ascending);
            }
        }
    }

    protected function getSorter(string $sortType)
    {
        switch ($sortType) {
            case self::SORT_NAME:
                $sorter = 'sortDeviceName';
                break;
            case self::SORT_SIZE:
                $sorter = 'sortDeviceSize';
                break;
            default:
                throw new RuntimeException('Invalid sort type');
        }

        return $sorter;
    }

    protected function sortDeviceName(FsObject $a, FsObject $b)
    {
        list($aSwapped, $bSwapped) = $this->getSwappedSortObjects($a, $b);

        return $aSwapped->getName() <=> $bSwapped->getName();
    }

    protected function sortDeviceSize(FsObject $a, FsObject $b)
    {
        list($aSwapped, $bSwapped) = $this->getSwappedSortObjects($a, $b);

        // Remember that directories don't have sizes
        $aSize = $aSwapped instanceof File ? $aSwapped->getSize() : 0;
        $bSize = $bSwapped instanceof File ? $bSwapped->getSize() : 0;

        return $aSize <=> $bSize;
    }

    /**
     * Exchanges the supplied sort pair based on the prevailing sort direction
     *
     * @param FsObject $a
     * @param FsObject $b
     * @return FsObject[]
     */
    protected function getSwappedSortObjects(FsObject $a, FsObject $b)
    {
        $aSwapped = $this->sortDirectionAscending ? $a : $b;
        $bSwapped = $this->sortDirectionAscending ? $b : $a;

        return [$aSwapped, $bSwapped];
    }

    protected function setSortDirection(bool $ascending)
    {
        $this->sortDirectionAscending = $ascending;
    }

    public function getTotalSize(): int
    {
        return $this->totalSize;
    }

    protected function setTotalSize(int $totalSize)
    {
        $this->totalSize = $totalSize;
    }

    protected function clearContents()
    {
        $this->contents = [];
        $this->populated = false;
    }

    protected function setPopulated()
    {
        $this->populated = true;
    }
}
