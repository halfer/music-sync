<?php

namespace MusicSync\Test\Unit;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use MusicSync\Service\FileOperation\FsObject;
use MusicSync\Test\DirectoryTestHarness;
use MusicSync\Test\TestCase;

class DirectoryTest extends TestCase
{
    public function testContentsMustBePopulated()
    {
        $pass = false;
        $dir = new Directory('home');
        try {
            $dir->getContents();
        }
        catch (\Exception $e) {
            $pass = ($e->getMessage() === 'Cannot get contents before population');
        }
        $this->assertTrue($pass, 'Expected exception thrown');
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

    /**
     * No need to have a descending recursive test also, I think
     */
    public function testRecursiveSortByNameAscending()
    {
        // Creates a nested structure
        $dir = $this->createDemoDirForNames();
        $dir->pushObjectPublic($this->createDemoDirForNames());

        $dir->recursiveSort(Directory::SORT_NAME);
        $this->assertEquals(
            $this->exploreDirectory($dir),
            [
                ['name' => 'a', ],
                ['name' => 'b', ],
                ['name' => 'c', ],
                ['name' => 'd', ],
                ['name' => 'home', 'contents' => [
                    ['name' => 'a', ],
                    ['name' => 'b', ],
                    ['name' => 'c', ],
                    ['name' => 'd', ],
                ]],
            ]
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

    public function testCalculatedDirectoryPath()
    {
        $this->markTestIncomplete();
    }

    /**
     * Not sure if a circular reference is possible?
     */
    public function testDetectDirectoryCircularReference()
    {
        $this->markTestIncomplete();
    }

    public function testVerifyDirectoryRelationships()
    {
        $this->markTestIncomplete();
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

    /**
     * No need to have a descending recursive test also, I think
     */
    public function testRecursiveSortBySizeAscending()
    {
        // Creates a nested structure
        $dir = $this->createDemoDirForSizes();
        $dir->pushObjectPublic($this->createDemoDirForSizes());

        $dir->recursiveSort(Directory::SORT_SIZE);
        $this->assertEquals(
            $this->exploreDirectory($dir),
            [
                ['name' => 'home', 'contents' => [
                    ['name' => 'd', 'size' => 15, ],
                    ['name' => 'b', 'size' => 25, ],
                    ['name' => 'a', 'size' => 50, ],
                    ['name' => 'c', 'size' => 75, ],
                ]],
                ['name' => 'd', 'size' => 15, ],
                ['name' => 'b', 'size' => 25, ],
                ['name' => 'a', 'size' => 50, ],
                ['name' => 'c', 'size' => 75, ],
            ]
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
