<?php

namespace MusicSync\Service\FileOperation;

class Directory extends FsObject
{
    protected array $contents = [];
    protected bool $populated = false;

    public function glob(string $pattern = '*')
    {
        $this->clearContents();
        foreach (glob($pattern) as $path)
        {
            $this->pushObject($path);
        }
        $this->setPopulated();
    }

    public function recursivePopulate()
    {
        foreach ($this->contents as $fsObject) {
            if ($fsObject instanceof Directory) {
                $path = $fsObject->getPath() .
                    DIRECTORY_SEPARATOR .
                    $fsObject->getName();
                $fsObject->glob($path);
                $fsObject->recursivePopulate();
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
