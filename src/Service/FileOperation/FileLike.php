<?php

namespace MusicSync\Service\FileOperation;

trait FileLike
{
    protected function nameContainsTrailingSeparator(string $name)
    {
        $lastChar = substr($name, -1, 1);

        return $lastChar === DIRECTORY_SEPARATOR;
    }

    // TODO Move size stuff here too
}