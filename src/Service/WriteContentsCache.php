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

    public function save(string $cachePath, string $name)
    {
        $cache = $this->getFactory()->createContentsCache();
        $data = $cache->serialise($this->getDirectory()->getContents());

        $this->createCacheDirectory($cachePath);
        $cacheFile = $this->getCachePath($cachePath, $name);

        $file = $this->getFactory()->createFile($cacheFile);
        $file->putContents($data);
    }

    protected function getDirectory(): Directory
    {
        return $this->directory;
    }

    protected function getCachePath(string $cachePath, string $name): string
    {
        return $cachePath . DIRECTORY_SEPARATOR . $name;
    }

    protected function createCacheDirectory(string $cachePath)
    {
        $dir = $this->getFactory()->createDirectory($cachePath);
        $dir->create();
    }

    protected function getFactory(): FileOperationFactory
    {
        return $this->factory;
    }
}
