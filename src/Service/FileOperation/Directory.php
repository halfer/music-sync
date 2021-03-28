<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

class Directory extends FsObject
{
    const POPULATE_OPTION_CONTENTS = 'contents';
    const POPULATE_OPTION_SIZE = 'size';

    protected array $contents = [];
    protected bool $populated = false;

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
