<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache;
use MusicSync\Test\TestCase;

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

    public function testPopulateFailsIfDirectoryDoesNotExist()
    {
        // Get the dir name wrong
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->getService()->create($dirPath . '2');

        // This fails, so let's mark it
        $this->fail('Need to get the create() method to throw exception');
    }

    public function testPopulateAndSaveEndToEnd()
    {
        $this->markTestIncomplete();
    }

    /**
     * @return WriteContentsCache
     */
    protected function getService(): WriteContentsCache
    {
        return $this->service;
    }
}
