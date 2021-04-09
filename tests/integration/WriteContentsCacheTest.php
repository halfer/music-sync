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
            // @todo Only flip the flag if the error is correct
            $failed = true;
        }

        $this->assertTrue(
            $failed,
            'Expecting an exception to be thrown'
        );
    }

    public function testDirectoryTraversalNotAllowedInName()
    {
        $this->markTestIncomplete();
    }

    public function testDirectorySeparatorNotAllowedInName()
    {
        $this->markTestIncomplete();
    }

    public function testSaveEncountersPermissionError()
    {
        // Create in-memory structure
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->getService()->create($dirPath);

        // Writing a file should fail here
        $cachePath = $this->getNewTempDir(__FUNCTION__ . 'Cache');
        chmod($cachePath, 0100);

        $failed = false;
        try {
            $this->getService()->save($cachePath, 'test.cache');
        } catch (RuntimeException $e) {
            // @todo Only flip the flag if the error is correct
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
