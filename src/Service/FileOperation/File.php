<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;
use MusicSync\Service\FileOperation\Exception\Permission as PermissionException;

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
        $path = $this->getPath() . DIRECTORY_SEPARATOR . $this->getName();
        if (!$this->isWriteable()) {
            throw new PermissionException(
                sprintf(
                    '%s is not writeable, check permissions?',
                    $path
                )
            );
        }

        return file_put_contents($path, $data);
    }
}
