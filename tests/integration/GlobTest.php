<?php

namespace MusicSync\Test\Integration;

use MusicSync\Test\TestCase;
use MusicSync\Service\FileOperation\Directory;

class GlobTest extends TestCase
{
    use ExampleStructures;

    public function setUp(): void
    {
        $this->wipeTempDir();
    }

    public function testNonRecursiveGlob()
    {
        // Set up files + folders
        $expectedFiles = ['a', 'b', ];
        $tmp = $this->getNewTempDir(__FUNCTION__);
        $this->createDemoFiles([$tmp], $expectedFiles);

        // Run the operation
        $dir = new Directory($tmp);
        $dir->glob('*');

        $actualFiles = [];
        foreach ($dir->getContents() as $content) {
            $actualFiles[] = $content->getName();
        }
        $this->assertEquals(['file_a', 'file_b', ], $actualFiles);
    }

    public function testRecursiveGlob()
    {
        $this->setUpRecursiveTestStructure(__FUNCTION__);

        // Run the operation
        $dir = new Directory($this->getNewTempDir(__FUNCTION__));
        $dir->recursivePopulate();

        // Test result
        $innerContents = [
            ['name' => 'file_a', ],
            ['name' => 'file_b', ],
            ['name' => 'file_c', ], ];
        $this->assertEquals(
            $this->exploreDirectory($dir),
            [
                ['name' => '1', 'contents' => $innerContents, ],
                ['name' => '2', 'contents' => $innerContents, ],
                ['name' => '3', 'contents' => $innerContents, ],
                ['name' => '4', 'contents' => $innerContents, ],
            ]
        );
    }

    public function testGlobCounts()
    {
        $parent = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->createDemoFiles([$parent], ['a', 'b', 'c', ]);
        $this->createDemoLinks([$parent], ['d', 'e', 'f', ]);
        @mkdir($parent . DIRECTORY_SEPARATOR . 'g');
        @mkdir($parent . DIRECTORY_SEPARATOR . 'h');

        $dir = new Directory($parent);
        $dir->glob();
        $this->assertEquals(3, $dir->getFileCount());
        $this->assertEquals(3, $dir->getLinkCount());
        $this->assertEquals(6, $dir->getDirCount());
    }

    public function testRecursiveGlobCounts()
    {
        $parent = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $firstDir = $parent . DIRECTORY_SEPARATOR . '1';
        @mkdir($firstDir . DIRECTORY_SEPARATOR . 'g');
        @mkdir($firstDir . DIRECTORY_SEPARATOR . 'h');
        $this->createDemoLinks(
            [$parent, $firstDir],
            ['d', 'e', 'f', ]
        );

        $dir = new Directory($parent);
        $dir->recursivePopulate();
        $this->assertEquals(
            12,
            $dir->getFileCountRecursive()
        );
        $this->assertEquals(6, $dir->getLinkCountRecursive());
        $this->assertEquals(6, $dir->getDirCountRecursive());
    }

    /**
     * @todo Add a symlink to the test data
     */
    public function testRecursiveSize()
    {
        $parent = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);

        // Run the operation
        $dir = new Directory($parent);
        $dir->recursivePopulate(true);

        // Test result
        $innerContents = [
            ['name' => 'file_a', 'size' => 6, ],
            ['name' => 'file_b', 'size' => 6, ],
            ['name' => 'file_c', 'size' => 6, ],
        ];
        $this->assertEquals(
            $this->exploreDirectory($dir),
            [
                ['name' => '1', 'contents' => $innerContents, ],
                ['name' => '2', 'contents' => $innerContents, ],
                ['name' => '3', 'contents' => $innerContents, ],
                ['name' => '4', 'contents' => $innerContents, ],
            ]
        );
    }

    /**
     * @todo Add a symlink to the test data
     */
    public function testRecursiveTotalSize()
    {
        $parent = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->createFiles($parent, ['d', 'e']);

        // Run the operation
        $dir = new Directory($parent);
        $dir->recursivePopulate(true);

        $this->assertEquals(84, $dir->getTotalSize());
    }

    /**
     * Ensures that double-population or double-totalling does not break results
     */
    public function testDupRecursiveTotalSize()
    {
        $parent = $this->getNewTempDir(__FUNCTION__);
        $this->setUpRecursiveTestStructure(__FUNCTION__);
        $this->createFiles($parent, ['d', 'e']);

        // Run the operation
        $dir = new Directory($parent);
        $dir->recursivePopulate(true);
        $dir->recursivePopulate(true);
        $dir->getTotalSize();
        $totalSize = $dir->getTotalSize();

        $this->assertEquals(84, $totalSize);
    }
}