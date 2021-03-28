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

    public function populateSize()
    {
        $this->setSize(
            filesize($this->getPath() . DIRECTORY_SEPARATOR . $this->getName())
        );
    }

    public function setSize(int $size)
    {
        $this->size = $size;
    }

    public function hasSize(): bool
    {
        return isset($this->size);
    }

    public function getSize(): int
    {
        if (!$this->hasSize()) {
            throw new RuntimeException(
                'Sizes must be populated before use'
            );
        }

        return $this->size;
    }
}
