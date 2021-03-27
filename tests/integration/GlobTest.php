<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Directory;
use PHPUnit\Framework\TestCase;

class GlobTest extends TestCase
{
    public function testNonRecursiveGlob()
    {
        // Set up some files to scan
        $expectedFiles = ['a', 'b', ];
        $tmp = $this->getTempDir('testNonRecursiveGlob');
        $this->touchFiles($tmp, $expectedFiles);

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
        $this->markTestIncomplete();
    }

    protected function getTempDir($name)
    {
        $testRoot = realpath(__DIR__ . '/..');
        $tmp = $testRoot . '/tmp/' . $name;
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