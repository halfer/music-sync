<?php

namespace MusicSync\Test\Integration;

use MusicSync\Test\TestCase;
use MusicSync\Service\FileOperation\Directory;

class GlobTest extends TestCase
{
    public function setUp(): void
    {
        $tmpDir = $this->getTempDir();
        exec(" rm -rf $tmpDir/*");
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
        $this->assertEquals($expectedFiles, $actualFiles);
    }

    public function testRecursiveGlob()
    {
        $this->setUpRecursiveTestStructure(__FUNCTION__);

        // Run the operation
        $dir = new Directory($this->getNewTempDir(__FUNCTION__));
        $dir->glob();
        $dir->recursivePopulate();

        // Test result
        $innerContents = [['name' => 'a', ], ['name' => 'b', ], ['name' => 'c', ], ];
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
        $this->setUpRecursiveTestStructure($parent);
        $this->createDemoFiles([$parent], ['a', 'b', 'c', ]);
        $this->createDemoLinks([$parent], ['d', 'e', 'f', ]);
        @mkdir($parent . DIRECTORY_SEPARATOR . 'g');
        @mkdir($parent . DIRECTORY_SEPARATOR . 'h');

        $dir = new Directory($parent);
        $dir->glob();
        $this->assertEquals(3, $dir->getFileCount());
        $this->assertEquals(3, $dir->getLinkCount());
        $this->assertEquals(2, $dir->getDirCount());
    }

    public function testRecursiveGlobTotals()
    {
        $this->markTestIncomplete();
    }

    public function testRecursiveSize()
    {
        $this->setUpRecursiveTestStructure(__FUNCTION__);

        // Run the operation
        $dir = new Directory($this->getNewTempDir(__FUNCTION__));
        $dir->glob();
        $dir->recursivePopulate(true);

        // Test result
        $innerContents = [
            ['name' => 'a', 'size' => 0, ],
            ['name' => 'b', 'size' => 0, ],
            ['name' => 'c', 'size' => 0, ],
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

    protected function setUpRecursiveTestStructure(string $parent)
    {
        $expectedFiles = ['a', 'b', 'c'];
        $this->createDemoFiles(
            $this->createDemoFolders($parent, ['1', '2', '3', '4',]),
            $expectedFiles
        );
    }

    protected function createDemoFolders($parent, array $names)
    {
        $tmps = [];
        foreach ($names as $name) {
            $tmps[] = $this->getNewTempDir($parent . DIRECTORY_SEPARATOR . $name);
        }

        return $tmps;
    }

    protected function createDemoFiles(array $tmps, array $expectedFiles)
    {
        foreach ($tmps as $tmp) {
            $this->touchFiles($tmp, $expectedFiles);
        }
    }

    protected function createDemoLinks(array $tmps, array $expectedLinks)
    {
        foreach ($tmps as $tmp) {
            $this->createLinks($tmp, $expectedLinks);
        }
    }

    protected function getTempDir()
    {
        $testRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

        return $testRoot . DIRECTORY_SEPARATOR . 'tmp';
    }

    protected function getNewTempDir($name)
    {
        $tmp = $this->getTempDir() . DIRECTORY_SEPARATOR . $name;
        @mkdir($tmp, 0777, true);

        return $tmp;
    }

    protected function touchFiles(string $parent, array $files)
    {
        foreach ($files as $file) {
            touch($parent . DIRECTORY_SEPARATOR . $file);
        }
    }

    protected function createLinks(string $parent, array $links)
    {
        $testRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
        $randomFile = $testRoot . DIRECTORY_SEPARATOR . 'bootstrap.php';
        foreach ($links as $link) {
            symlink($randomFile, $parent . DIRECTORY_SEPARATOR . $link);
        }
    }
}