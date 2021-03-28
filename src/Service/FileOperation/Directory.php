<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

class Directory extends FsObject
{
    const SORT_NAME = 'name';
    const SORT_SIZE = 'size';

    protected array $contents = [];
    protected bool $populated = false;
    protected bool $sortDirectionAscending = true;

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
     * @param bool $popSize
     */
    public function recursivePopulate(bool $popSize = false)
    {
        foreach ($this->getContents() as $fsObject) {
            if ($fsObject instanceof Directory) {
                $fsObject->glob();
                $fsObject->recursivePopulate($popSize);
            }
            if ($popSize && $fsObject instanceof File) {
                $fsObject->populateSize();
            }
        }
    }

    protected function pushObject(string $path)
    {
        if (is_file($path)) {
            $this->contents[] = new File($path);
        } elseif (is_dir($path)) {
            $this->contents[] = new Directory($path);
        }
    }

    public function getContents()
    {
        if (!$this->populated) {
            throw new RuntimeException('Cannot get contents before population');
        }

        return $this->contents;
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

        return $aSwapped->getSize() <=> $bSwapped->getSize();
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
