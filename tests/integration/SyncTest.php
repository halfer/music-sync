<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\File;
use MusicSync\Service\SyncFiles;
use MusicSync\Test\DirectoryTestHarness as TestDirectory;
use MusicSync\Test\TestCase;

class SyncTest extends TestCase
{
    protected TestFileOperationFactory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new TestFileOperationFactory();
    }

    public function testSimpleCase()
    {
        // Build some dir structures in memory
        $source = $this->createStructure1();
        $dest = $this->createStructure2();

        // Insert them into the sync system
        $sut = new SyncFiles($this->factory);
        $sut
            ->setSourceDirectory($source)
            ->setDestinationDirectory($dest);

        $this->markTestIncomplete();
    }

    protected function createStructure1(): TestDirectory
    {
        // Upper level objects
        $dirD = new TestDirectory('/home/person/d');
        $this->pushObjects($dirD, [
            new File('d-a'),
            new File('d-c'),
        ]);
        $dirE = new TestDirectory('/home/person/e');
        $this->pushObjects($dirE, [
            new File('e-a'),
            new File('e-b'),
            new File('e-c'),
        ]);

        // Main level objects
        $dir = $this->createBaseDirectory();
        $this->pushObjects($dir, [
            new File('a'),
            new File('b'),
            // Missing file "c"
            $dirD,
            $dirE,
            new TestDirectory('f'),
        ]);

        return $dir;
    }

    protected function createStructure2(): TestDirectory
    {
        // Upper level objects
        $dirD = new TestDirectory('/home/person/d');
        $this->pushObjects($dirD, [
            new File('d-a'),
            new File('d-b'),
        ]);

        // Main level objects
        $dir = $this->createBaseDirectory();
        $this->pushObjects($dir, [
            new File('a'),
            // Missing file "b"
            new File('c'),
            $dirD,
            // Missing dir "e"
            new TestDirectory('f'),
        ]);

        return $dir;
    }

    protected function createBaseDirectory(): TestDirectory
    {
        // Injects a factory that always creates test directories :=)
        $dir = new TestDirectory('/home/person');
        $dir->setFactory($this->factory);

        return $dir;
    }

    // @todo Remove this and move calls above to class copy
    protected function pushObjects(TestDirectory $directory, array $fsObjects)
    {
        foreach ($fsObjects as $fsObject) {
            $directory->pushObjectPublic($fsObject);
        }
    }
}