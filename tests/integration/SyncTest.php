<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\File;
use MusicSync\Test\DirectoryTestHarness as TestDirectory;
use MusicSync\Test\TestCase;
use PHPUnit\Util\Test;

class SyncTest extends TestCase
{
    public function testSimpleCase()
    {
        // Just making sure they run at the mo!
        $this->createStructure1();
        $this->createStructure2();

        $this->markTestIncomplete();
    }

    protected function createStructure1()
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
    }

    protected function createStructure2()
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
    }

    protected function createBaseDirectory()
    {
        return new TestDirectory('/home/person');
    }

    // @todo Remove this and move calls above to class copy
    protected function pushObjects(TestDirectory $directory, array $fsObjects)
    {
        foreach ($fsObjects as $fsObject) {
            $directory->pushObjectPublic($fsObject);
        }
    }
}