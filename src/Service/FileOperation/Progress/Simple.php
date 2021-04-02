<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

/**
 * A progress device that counts based on the number of
 * objects in the Directory root
 *
 * @note The Progress classes are just sketch-only at this stage
 */
class Simple implements Progress
{
    public function scanDirectory(Directory $dir)
    {

    }

    /**
     * Just experimenting with some ideas here about how to declare
     * where we are in the hierarchy.
     *
     *  A       B     C      D
     * ------  ---   ----   ---
     * D E  F  GHI   JKLM   NOP
     *   |
     *   Q
     */
    public function declarePosition(int $rootPosition, int $fileCount)
    {

    }

    public function getPercent(): int
    {

    }

    /**
     * The Simple progress device is always available
     *
     * @return bool
     */
    public function isOperational(): bool
    {
        return true;
    }
}
