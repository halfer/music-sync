<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache;
use MusicSync\Test\TestCase;
use RuntimeException;

class WriteContentsCacheTest extends TestCase
{
    use ExampleStructures;

    protected WriteContentsCache $service;

    public function setUp(): void
    {
        $this->wipeTempDir();
        $this->service = new WriteContentsCache(new FileOperationFactory());
    }

    /**
     * @todo Most of this is for the e2e
     */
    public function testPopulateSucceeds()
    {
        // Create in-memory structure
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->getService()->create($dirPath);

        // If it doesn't fail, let's call that a success
        $this->assertTrue(true);
    }

    public function testPopulateFailsIfDirectoryDoesNotExist()
    {
        $dirPath = $this->getNewTempDir(__FUNCTION__);

        // Get the dir name wrong
        $failed = false;
        try {
            $this->getService()->create($dirPath . '2');
        }
        catch (RuntimeException $e) {
            $failed = true;
        }

        $this->assertTrue(
            $failed,
            'Expecting an exception to be thrown'
        );
    }

    public function testPopulateAndSaveEndToEnd()
    {
        // Create in-memory structure
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->getService()->create($dirPath);

        // Write serialised representation
        $cachePath = $this->getNewTempDir(__FUNCTION__ . 'Cache');
        $this->getService()->save($cachePath, 'test.cache');

        // Check it produces JSON
        $cacheFile = $cachePath . DIRECTORY_SEPARATOR . 'test.cache';
        $this->assertTrue(
            is_array(
                json_decode(
                    file_get_contents($cacheFile),
                    true
                )
            )
        );
    }

    /**
     * @return WriteContentsCache
     */
    protected function getService(): WriteContentsCache
    {
        return $this->service;
    }
}
