<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

trait FileLike
{
    public function isWriteable(): bool
    {
        $filePath = $this->getPath() . DIRECTORY_SEPARATOR . $this->getName();

        // is_writeable() needs the file to exist...
        if (file_exists($filePath)) {
            $writeable = is_writeable($filePath);
        // ... so if it doesn't exist we check the writeability of the parent
        } elseif (file_exists($this->getPath())) {
            $writeable = is_writeable($this->getPath());
        } else {
            // ... and if that does not exist, something is very wrong!
            throw new RuntimeException('Parent folder does not exist');
        }

        return $writeable;
    }

    protected function nameContainsTrailingSeparator(string $name)
    {
        $lastChar = substr($name, -1, 1);

        return $lastChar === DIRECTORY_SEPARATOR;
    }

    public function populateSize(): int
    {
        $size = filesize($this->getPath() . DIRECTORY_SEPARATOR . $this->getName());
        $this->setSize($size);

        return $size;
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
