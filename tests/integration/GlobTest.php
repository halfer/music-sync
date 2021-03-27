<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Directory;
use PHPUnit\Framework\TestCase;

class GlobTest extends TestCase
{
    public function testNonRecursiveGlob()
    {
        $tmp = $this->getTempDir('testNonRecursiveGlob');
        touch($tmp . '/a');
        touch($tmp . '/b');
        $dir = new Directory($tmp);
        $dir->glob('*');
        print_r($dir->getContents());
        $this->markTestIncomplete();
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
}