<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

class Link extends FsObject
{
    use FileLike;

    public function __construct(string $name)
    {
        if ($this->nameContainsTrailingSeparator($name)) {
            throw new RuntimeException(
                'A directory separator cannot be the last character in a symlink'
            );
        }
        parent::__construct($name);
    }
}
