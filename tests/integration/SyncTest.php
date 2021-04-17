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

    public function testSimpleSyncCase()
    {
        // Build some dir structures in memory
        $source = $this->createStructure1();
        $dest = $this->createStructure2();

        // Insert them into the sync system
        $sut = new SyncFiles($this->factory);
        $sut
            ->setSourceDirectory($source)
            ->setDestinationDirectory($dest)
            ->sync();

        $this->markTestIncomplete();
    }

    /**
     * @todo These objects fail validation, their parents are not set
     */
    protected function createStructure1(): TestDirectory
    {
        $home = '/home/person';

        // Upper level objects
        $dirD = new TestDirectory($home . '/d');
        $dirD->pushObjects([
            new File($home . '/d-a'),
            new File($home . '/d-c'),
        ]);
        $dirE = new TestDirectory($home . '/e');
        $dirE->pushObjects([
            new File($home . '/e-a'),
            new File($home . '/e-b'),
            new File($home . '/e-c'),
        ]);
        $dirF = new TestDirectory($home . '/f');
        $dirF->setContents([]); // Marks as populated

        // Main level objects
        $dir = $this->createBaseDirectory();
        $dir->pushObjects([
            new File($home . '/a'),
            new File($home . '/b'),
            // Missing file "c"
            $dirD,
            $dirE,
            $dirF,
        ]);

        return $dir;
    }

    /**
     * @todo These objects fail validation, their parents are not set
     */
    protected function createStructure2(): TestDirectory
    {
        $home = '/home/person';

        // Upper level objects
        $dirD = new TestDirectory($home . '/d');
        $dirD->pushObjects([
            new File($home . '/d-a'),
            new File($home . '/d-b'),
        ]);
        $dirF = new TestDirectory($home . '/f');
        $dirF->setContents([]); // Marks as populated

        // Main level objects
        $dir = $this->createBaseDirectory();
        $dir->pushObjects([
            new File($home . '/a'),
            // Missing file "b"
            new File($home . '/c'),
            $dirD,
            // Missing dir "e"
            $dirF,
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
}
