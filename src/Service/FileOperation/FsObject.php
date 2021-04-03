<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

abstract class FsObject
{
    protected string $name;
    protected ?Directory $parent;
    protected ?string $path = null;

    public function __construct(string $name, Directory $parent = null)
    {
        // The name cannot be empty
        if (!$name) {
            throw new \RuntimeException(
                'An object name cannot be empty'
            );
        }

        // Split a name into path/name components
        $lastSlash = strrpos($name, DIRECTORY_SEPARATOR);
        if ($lastSlash !== false) {
            $this->path = substr($name, 0, $lastSlash);
            $name = substr($name, $lastSlash + 1);
        }

        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getParent(): Directory
    {
        return $this->parent;
    }
}
