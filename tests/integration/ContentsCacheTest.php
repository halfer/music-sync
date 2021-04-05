<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\ContentsCache;
use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\FileOperation\FsObject;
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

        $jsonExpected = $this->fetchDataFile('serialised.json');
        $this->assertEquals(
            json_decode($jsonExpected, true),
            json_decode($serialised, true)
        );
    }

    public function testDeserialiseDirectory()
    {
        // Fetch the raw
        $json = $this->fetchDataFile('serialised.json');
        $wrapper = json_decode($json, true);

        // Do a deserialisation
        $cache = new ContentsCache();
        $deserialised = $cache->deserialise($json);

        $this->comparator(
            $wrapper['data'],
            $deserialised
        );
    }

    // Func to compare array and object formats
    public function comparator(array $arrays, array $fsObjects)
    {
        foreach ($arrays as $arrayEntry) {
            /* @var FsObject $fsObject */
            $fsObject = current($fsObjects);
            $type = $arrayEntry['type'];

            // Compare array to object elements
            $this->assertEquals(
                $arrayEntry['name'],
                $fsObject->getName()
            );
            $this->assertEquals(
                $type,
                $this->convertObjectTypeToString($fsObject)
            );

            // @todo Call self recursively for directories

            next($fsObjects);
        }
    }

    protected function convertObjectTypeToString(FsObject $fsObject)
    {
        $fullName = get_class($fsObject);

        // Converts `MusicSync\Service\FileOperation\Thing` to just `Thing`
        return substr($fullName, strrpos($fullName, '\\') + 1);
    }

    protected function fetchDataFile(string $file)
    {
        return file_get_contents(
            $this->getTestDir() . '/data/' . $file
        );
    }
}
