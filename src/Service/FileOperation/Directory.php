<?php

namespace MusicSync\Service\FileOperation;

class Directory extends FsObject
{
    protected array $contents = [];

    public function glob(string $pattern, $recursive = false)
    {
        $this->clearContents();
        foreach (glob($pattern) as $path)
        {

        }
    }

    protected function pushObject(string $path)
    {
        if (is_file($path))
        {
            $this->contents = new File($path);
        }
    }

    protected function clearContents()
    {
        $this->contents = [];
    }
}
