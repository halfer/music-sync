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
    protected int $totalSize = 0;
    protected ?Factory $factory = null;

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
        $this->glob();
        foreach ($this->getContents() as $fsObject) {
            if ($fsObject instanceof Directory) {
                $fsObject->glob();
                $fsObject->recursivePopulate($popSize);
            }
            if ($popSize && $fsObject instanceof File) {
                $fsObject->populateSize();
                // How to reset the size?
                // How to announce the size to parents?
                // Get a class dump working to figure this out!
            }
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

    public function getContents()
    {
        if (!$this->populated) {
            throw new RuntimeException('Cannot get contents before population');
        }

        return $this->contents;
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
        $totals = $this->countObjectsByTypeRecursively2();

        return $totals[0];
    }

    /**
     * Counts symlinks across the whole structure
     *
     * @return int
     */
    public function getLinkCountRecursive()
    {
        $totals = $this->countObjectsByTypeRecursively2();

        return $totals[1];
    }

    protected function countObjectsByTypeRecursively2()
    {
        $totalFile = $totalLink = $totalDir = 0;

        foreach ($this->getContents() as $fsObject) {
            /* @var $fsObject FsObject */
            if ($fsObject instanceof Directory) {
                list($subTotalFile,
                     $subTotalLink,
                     $subTotalDir)= $fsObject->countObjectsByTypeRecursively2();
                $totalFile += $subTotalFile;
                $totalLink += $subTotalLink;
                $totalDir += $subTotalDir;
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

    /**
     * Sets a custom object factory if required
     *
     * @param Factory $factory
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
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

    protected function clearContents()
    {
        $this->contents = [];
        $this->populated = false;
    }

    protected function setPopulated()
    {
        $this->populated = true;
    }

    protected function getFactory()
    {
        // Only create a default one once
        static $factory = null;
        if (!$this->factory) {
            $factory = new Factory();
        }

        // Prefer the custom one, but use the default one otherwise
        return $this->factory ?: $factory;
    }
}
