<?php

namespace MusicSync\Service\FileOperation;

class Factory
{
    public function createFile(string $name): File
    {
        return new File($name);
    }

    public function createDirectory(string $name): Directory
    {
        return new Directory($name);
    }
}
