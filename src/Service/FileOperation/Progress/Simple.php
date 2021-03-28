<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

/**
 * A progress device that counts based on the number of
 * objects in the Directory root
 */
class Simple implements Progress
{
    public function scanDirectory(Directory $dir)
    {

    }

    public function getPercent(): int {

    }
}
