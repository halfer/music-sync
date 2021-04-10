<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Exception\Permission as PermissionException;
use MusicSync\Service\FileOperation\Factory as FileOperationFactory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\FileOperation\Directory;
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
            $failed = ($e->getMessage() === 'Directory does not exist');
        }

        $this->assertTrue(
            $failed,
            'Expecting an exception to be thrown'
        );
    }

    public function testSaveBeforePopulationNotAllowed()
    {
        $cachePath = $this->getNewTempDir(__FUNCTION__ . 'Cache');

        $failed = false;
        try {
            $this->getService()->save($cachePath, 'test.cache');
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'Populate the cache before trying to save it') {
                $failed = true;
            }
        }
        $this->assertTrue($failed);
    }

    public function testDirectoryTraversalNotAllowedInName()
    {
        $this->markTestIncomplete();
    }

    public function testDirectorySeparatorNotAllowedInName()
    {
        $this->markTestIncomplete();
    }

    /**
     * Ah, we have a problem emulating permission issues here
     *
     * @todo Create a special permission error and get File to raise it
     * @todo Create a custom File that raises a permission exception in putContents
     * @todo Create a custom factory that uses the custom File class
     */
    public function testSaveEncountersPermissionError()
    {
        // Create test harness service
        $service = new WriteContentsCache(new FactoryTestHarness());

        // Create in-memory structure
        $dirPath = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $service->create($dirPath);

        $cachePath = $this->getNewTempDir(__FUNCTION__ . 'Cache');
        $failed = false;
        try {
            // Writing a file should fail here
            $service->save($cachePath, 'test.cache');
        } catch (PermissionException $e) {
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

/**
 * Special File class that can emulate a permission exception
 */
class FileTestHarness extends File
{
    protected bool $permissionError = false;

    public function emulatePermissionError()
    {
        $this->permissionError = true;
    }

    public function putContents(string $data)
    {
        if ($this->permissionError) {
            throw new PermissionException(
                'Emulated permission error'
            );
        }

        return parent::putContents($data);
    }
}

/**
 * Special Factory that creates an explosive File class :=)
 */
class FactoryTestHarness extends FileOperationFactory
{
    public function createFile(string $name, Directory $parent = null): File
    {
        $file = new FileTestHarness($name, $parent);
        $file->emulatePermissionError();

        return $file;
    }
}
