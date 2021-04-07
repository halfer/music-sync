<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\ContentsCache;
use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\FileOperation\FileLike;
use MusicSync\Service\FileOperation\FsObject;
use MusicSync\Test\TestCase;

class ContentsCacheTest extends TestCase
{
    use ExampleStructures;

    public function testSerialiseDirectoryWithSizes()
    {
        $this->runTestSerialiseDirectory(true, 'serialised-with-sizes.json');
    }

    public function testSerialiseDirectoryWithoutSizes()
    {
        $this->runTestSerialiseDirectory(false, 'serialised-without-sizes.json');
    }

    public function runTestSerialiseDirectory(bool $popSize, string $dataFile)
    {
        $dirName = 'testSerialiseDirectory';
        $this->setUpRecursiveTestStructure($dirName);

        // Run the operation
        $dir = new Directory($this->getNewTempDir($dirName));
        $dir->recursivePopulate($popSize);

        // Serialise the demo structure
        $cache = new ContentsCache();
        $serialised = $cache->serialise($dir->getContents());

        $jsonExpected = $this->fetchDataFile($dataFile);
        $this->assertEquals(
            json_decode($jsonExpected, true),
            json_decode($serialised, true)
        );
    }

    public function testDeserialiseDirectoryWithSizes()
    {
        $this->runTestDeserialiseDirectory('serialised-with-sizes.json');
    }

    public function testDeserialiseDirectoryWithoutSizes()
    {
        $this->runTestDeserialiseDirectory('serialised-without-sizes.json');
    }

    public function runTestDeserialiseDirectory(string $dataFile)
    {
        // Fetch the raw data
        $json = $this->fetchDataFile($dataFile);
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
    protected function comparator(array $arrays, array $fsObjects)
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
            if ($type === 'File' || $type === 'Link') {
                /* @var $fsObject FileLike */
                if (isset($arrayEntry['size'])) {
                    $this->assertEquals(
                        $arrayEntry['size'],
                        $fsObject->getSize()
                    );
                }
            }
            $this->assertEquals(
                $type,
                $this->convertObjectTypeToString($fsObject)
            );

            // Call self recursively for directories
            if ($type === 'Directory') {
                /* @var Directory $fsObject */
                $this->comparator(
                    $arrayEntry['contents'],
                    $fsObject->getContents()
                );
            }

            next($fsObjects);
        }
    }

    /**
     * @param FsObject $fsObject
     * @return string
     */
    protected function convertObjectTypeToString(FsObject $fsObject): string
    {
        $fullName = get_class($fsObject);

        // Converts `MusicSync\Service\FileOperation\Thing` to just `Thing`
        return substr($fullName, strrpos($fullName, '\\') + 1);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function fetchDataFile(string $file): string
    {
        return file_get_contents(
            $this->getTestDir() . '/data/' . $file
        );
    }
}
