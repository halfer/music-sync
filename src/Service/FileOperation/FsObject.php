<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

abstract class FsObject
{
    protected string $name;
    protected ?string $path = null;

    public function __construct(string $name)
    {
        // Split a name into path/name components
        $lastSlash = strrpos($name, DIRECTORY_SEPARATOR);
        if ($lastSlash !== false) {
            $this->path = substr($name, 0, $lastSlash);
            $name = substr($name, $lastSlash + 1);
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}
