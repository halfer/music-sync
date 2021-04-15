<?php

namespace MusicSync\Test\Integration;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\Factory;
use MusicSync\Test\DirectoryTestHarness;

class TestFileOperationFactory extends Factory
{
    /**
     * Creates a special test Directory rather than the normal one
     *
     * @param string $name
     * @param Directory|null $parent
     * @return DirectoryTestHarness
     */
    public function createDirectory(string $name, Directory $parent = null): DirectoryTestHarness
    {
        return new DirectoryTestHarness($name, $parent);
    }
}
