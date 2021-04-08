<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache;
use MusicSync\Test\TestCase;

class WriteContentsCacheTest extends TestCase
{
    use ExampleStructures;

    public function setUp(): void
    {
        $this->wipeTempDir();
    }

    public function testPopulateSucceeds()
    {
        $service = new WriteContentsCache(new FileOperationFactory());

        // Create in-memory structure
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $service->create($dirPath);

        // Write serialised representation
        $cachePath = $this->getNewTempDir(__FUNCTION__ . 'Cache');
        $service->save($cachePath, 'test.cache');

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
        $service = new WriteContentsCache(new FileOperationFactory());
        $service->create($this->getNewTempDir(__DIR__));
        $this->markTestIncomplete();
    }

    public function testPopulateAndSaveEndToEnd()
    {
        $this->markTestIncomplete();
    }
}
