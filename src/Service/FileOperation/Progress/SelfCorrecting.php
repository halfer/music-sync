<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

/**
 * A progress device that initially assumes all directories in the
 * root have the same number of objects contained within, and the
 * percentage is increased/decreased as more information is discovered.
 *
 * @todo Implement the interface once it has settled down
 */
class SelfCorrecting #implements Progress
{
    public function scanDirectory(Directory $dir)
    {

    }

    public function getPercent(): int
    {

    }

    public function isOperational(): bool
    {

    }
}
