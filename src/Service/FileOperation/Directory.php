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

        foreach (glob($path) as $path)
        {
            $this->pushObject($path);
        }
        $this->setPopulated();
    }

    public function recursivePopulate(array $options = [])
    {
        // Defaults to true
        $popContents = isset($options[self::POPULATE_OPTION_CONTENTS]) ?
            (bool) $options[self::POPULATE_OPTION_CONTENTS] :
            true;
        // Defaults to false
        $popSize = isset($options[self::POPULATE_OPTION_SIZE]) ?
            (bool) $options[self::POPULATE_OPTION_SIZE] :
            false;

        foreach ($this->getContents() as $fsObject) {
            if ($popContents && $fsObject instanceof Directory) {
                $fsObject->glob();
                $fsObject->recursivePopulate();
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
