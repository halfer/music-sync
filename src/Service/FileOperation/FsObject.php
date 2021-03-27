<?php

namespace MusicSync\Service\FileOperation;

abstract class FsObject
{
    protected string $name;
    protected ?Directory $parent = null;

    public function __construct(string $name, Directory $parent = null)
    {
        // @todo Throw an exception if there is a separator in the name

        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
