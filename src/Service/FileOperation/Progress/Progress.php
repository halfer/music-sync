<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

/**
 * @note The Progress classes are just sketch-only at this stage
 */
interface Progress
{
    public function scanDirectory(Directory $dir);

    public function getPercent(): int;

    public function isOperational(): bool;
}
