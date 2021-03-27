<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Directory;
use PHPUnit\Framework\TestCase;

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
        $tmp = $this->getNewTempDir('testNonRecursiveGlob');
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
        // Set up files + folders
        $expectedFiles = ['a', 'b', 'c'];
        $this->createDemoFiles(
            $this->createDemoFolders('testRecursiveGlob', ['1', '2', '3', '4',]),
            $expectedFiles
        );

        // Run the operation
        $dir = new Directory($this->getNewTempDir('testRecursiveGlob'));
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

    /**
     * This does not work
     */
    protected function exploreDirectory(Directory $dir) {
        $list = [];
        foreach ($dir->getContents() as $item) {
            $entry = ['name' => $item->getName(), ];
            if ($item instanceof Directory) {
                $entry['contents'] = $this->exploreDirectory($item);
            }
            $list[] = $entry;
        }

        return $list;
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
}