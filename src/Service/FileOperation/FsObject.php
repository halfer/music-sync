<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

abstract class FsObject
{
    protected string $name;
    protected ?Directory $parent = null;

    public function __construct(string $name, Directory $parent = null)
    {
        // Throw an exception if there is a separator in the name
        if (str_contains($name, DIRECTORY_SEPARATOR)) {
            throw new RuntimeException(
                'FsObject names cannot contain directory separators'
            );
        }

        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
