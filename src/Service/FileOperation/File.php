<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

class File extends FsObject
{
    protected int $size;

    public function __construct(string $name)
    {
        $lastChar = substr($name, -1, 1);
        if ($lastChar === DIRECTORY_SEPARATOR) {
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
