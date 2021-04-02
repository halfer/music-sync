<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

class File extends FsObject
{
    use FileLike;

    protected int $size;

    public function __construct(string $name)
    {
        if ($this->nameContainsTrailingSeparator($name)) {
            throw new RuntimeException(
                'A directory separator cannot be the last character in a file'
            );
        }
        parent::__construct($name);
    }

    public function putContents(string $data)
    {
        return file_put_contents(
            $this->getPath() . DIRECTORY_SEPARATOR . $this->getName()
        );
    }
}
