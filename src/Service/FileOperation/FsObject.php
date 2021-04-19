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

        // We can't check for circular references set, since
        // at this point, if this is a directory, the content
        // is not yet populated. So we just set the parent
        // property, and defer detecting an invalid structure
        // until the content is set.
        if ($parent) {
            $this->parent = $parent;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getParent(): ?Directory
    {
        return $this->parent;
    }

    public function setParent(Directory $parent)
    {
        if ($parent === $this) {
            throw new RuntimeException('A directory cannot be its own parent');
        }

        // Ensure that a parent is not also a child
        if ($this instanceof Directory) {
            $this->checkCircularReferences($this, $parent);
        }

        $this->parent = $parent;
    }

    protected function checkCircularReferences(Directory $dir, Directory $parent)
    {
        foreach ($dir->iterator() as $fsObject) {
            if ($fsObject instanceof Directory) {
                if ($fsObject === $parent) {
                    throw new RuntimeException(
                        'Cannot set dir as parent since it is also a descendant'
                    );
                }
            }
        }
    }
}
