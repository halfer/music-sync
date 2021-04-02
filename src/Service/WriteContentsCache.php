<?php

namespace MusicSync\Service;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;

class WriteContentsCache
{
    protected FileOperationFactory $factory;
    protected Directory $directory;

    public function __construct(FileOperationFactory $factory)
    {
        $this->factory = $factory;
    }

    public function create(string $dirPath)
    {
        $dir = $this->getFactory()->createDirectory($dirPath);
        $dir->recursivePopulate(true);
        $this->directory = $dir;
    }

    public function save(string $name, string $cachePath)
    {
        $cache = $this->getFactory()->createContentsCache();
        $data = $cache->serialise($this->getDirectory()->getContents());
        $file = $this->getFactory()->createFile($cachePath);
        $file->putContents($data);
    }

    protected function getDirectory(): Directory
    {
        return $this->directory;
    }

    protected function getFactory(): FileOperationFactory
    {
        return $this->factory;
    }
}
