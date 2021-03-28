<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

trait FileLike
{
    protected function nameContainsTrailingSeparator(string $name)
    {
        $lastChar = substr($name, -1, 1);

        return $lastChar === DIRECTORY_SEPARATOR;
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
