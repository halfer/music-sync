<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\WriteContentsCache;
use MusicSync\Test\TestCase;

class WriteContentsCacheTest extends TestCase
{
    use ExampleStructures;

    public function testPopulateSucceeds()
    {
        $service = new WriteContentsCache(new FileOperationFactory());
        $service->create($this->getNewTempDir(__DIR__));
        $this->markTestIncomplete();
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
