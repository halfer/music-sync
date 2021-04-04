<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\ContentsCache;
use MusicSync\Service\FileOperation\Directory;
use MusicSync\Test\TestCase;

class ContentsCacheTest extends TestCase
{
    use ExampleStructures;

    public function testSerialiseDirectory()
    {
        $this->setUpRecursiveTestStructure(__FUNCTION__);

        // Run the operation
        $dir = new Directory($this->getNewTempDir(__FUNCTION__));
        $dir->recursivePopulate(true);

        // Serialise the demo structure
        $cache = new ContentsCache();
        $serialised = $cache->serialise($dir->getContents());

        $jsonExpected = file_get_contents(
            $this->getTestDir() . '/data/serialised.json'
        );
        $this->assertEquals(
            json_decode($jsonExpected, true),
            json_decode($serialised, true)
        );
    }

    public function testDeserialiseDirectory()
    {
        $this->markTestIncomplete();
    }
}
