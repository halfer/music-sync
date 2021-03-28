<?php

namespace MusicSync\Test\Unit;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\FileOperation\FsObject;
use MusicSync\Test\TestCase;

class DirectoryTest extends TestCase
{
    public function testContentsMustBePopulated()
    {
        $this->markTestIncomplete();
    }

    public function testSortByNameAscending()
    {
        $dir = $this->createDemoDirForNames();

        $dir->sort(Directory::SORT_NAME);
        $this->assertEquals(
            ['a', 'b', 'c', 'd', ],
            $this->getFilenameList($dir)
        );
    }

    public function testSortByNameDescending()
    {
        $dir = $this->createDemoDirForNames();

        $dir->sort(Directory::SORT_NAME, false);
        $this->assertEquals(
            ['d', 'c', 'b', 'a', ],
            $this->getFilenameList($dir)
        );
    }

    protected function createDemoDirForNames()
    {
        $dir = new DirectoryTestHarness('home');
        foreach (['c', 'a', 'd', 'b'] as $fileName) {
            $dir->pushObjectPublic(new File($fileName));
        }

        return $dir;
    }

    public function testSortBySizeAscending()
    {
        $dir = $this->createDemoDirForSizes();

        $dir->sort(Directory::SORT_SIZE);
        $this->assertEquals(
            ['d', 'b', 'a', 'c', ],
            $this->getFilenameList($dir)
        );
    }

    public function testSortBySizeDescending()
    {
        $dir = $this->createDemoDirForSizes();

        $dir->sort(Directory::SORT_SIZE, false);
        $this->assertEquals(
            ['c', 'a', 'b', 'd', ],
            $this->getFilenameList($dir)
        );
    }

    protected function createDemoDirForSizes()
    {
        $dir = new DirectoryTestHarness('home');
        $files = [
            'a' => 50,    'b' => 25,
            'c' => 75,    'd' => 15,
        ];
        foreach ($files as $fileName => $size) {
            $file = new File($fileName);
            $file->setSize($size);
            $dir->pushObjectPublic($file);
        }

        return $dir;
    }

    protected function getFilenameList(Directory $dir)
    {
        $list = [];
        foreach ($dir->getContents() as $entry) {
            /* @var FsObject $entry */
            $list[] = $entry->getName();
        }

        return $list;
    }
}

/**
 * Not everyone likes this form of testing - tampering with the public
 * nature of methods to expose inner workings. But I think it is pretty
 * harmless in this case.
 */
class DirectoryTestHarness extends Directory
{
    public function pushObjectPublic(FsObject $object)
    {
        $this->contents[] = $object;
        $this->setPopulated();
    }
}
