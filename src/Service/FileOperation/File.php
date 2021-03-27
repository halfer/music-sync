<?php

namespace MusicSync\Service\FileOperation;

class File extends FsObject
{
    public function __construct(string $name)
    {
        $lastChar = substr($name, -1, 1);
        if ($lastChar === DIRECTORY_SEPARATOR) {
            throw new \RuntimeException(
                'A directory separator cannot be the last character in a file'
            );
        }
        parent::__construct($name);
    }
}
