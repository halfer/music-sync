<?php

namespace MusicSync\Service;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use RuntimeException;

class WriteContentsCache
{
    protected FileOperationFactory $factory;
    protected Directory $directory;

    public function __construct(FileOperationFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Creates an in-memory index of the specified directory
     *
     * @todo Throw a custom error rather than RuntimeException
     * @param string $dirPath
     */
    public function create(string $dirPath)
    {
        // Throw exception if the dir does not exist
        $dir = $this->getFactory()->createDirectory($dirPath);
        if (!$dir->exists()) {
            throw new RuntimeException(
                'Directory does not exist'
            );
        }

        // Now populate the dir memory structure
        $dir->recursivePopulate(true);
        $this->directory = $dir;
    }

    /**
     * Saves a string representation of the in-memory index
     *
     * @todo Return boolean success
     * @param string $cachePath
     * @param string $name
     */
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
        if (!isset($this->directory)) {
            throw new RuntimeException(
                'Populate the cache before trying to save it'
            );
        }

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
