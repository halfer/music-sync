<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

/**
 * A progress device that counts based on the prior
 * total object count in a prior run
 *
 * @todo Implement the interface once it has settled down
 */
class Cache #implements Progress
{
    public function scanDirectory(Directory $dir)
    {

    }

    public function getPercent(): int
    {

    }

    public function isOperational(): bool
    {
        // True if a cache file exists for this directory
    }
}
