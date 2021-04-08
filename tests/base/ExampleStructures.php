<?php

namespace MusicSync\Test\Integration;

trait ExampleStructures
{
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
            $this->createFiles($tmp, $expectedFiles);
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

    protected function wipeTempDir()
    {
        $tmpDir = $this->getTempDir();
        exec(" rm -rf $tmpDir/*");
    }

    protected function createFiles(string $parent, array $files)
    {
        foreach ($files as $file) {
            file_put_contents(
                $parent . DIRECTORY_SEPARATOR . 'file_' . $file,
                "hello!"
            );
        }
    }

    protected function createLinks(string $parent, array $links)
    {
        $testRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
        $randomFile = $testRoot . DIRECTORY_SEPARATOR . 'bootstrap.php';
        foreach ($links as $link) {
            symlink(
                $randomFile,
                $parent . DIRECTORY_SEPARATOR . 'link_' . $link);
        }
    }
}
