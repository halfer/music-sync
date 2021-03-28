<?php

namespace MusicSync\Service\FileOperation\Progress;

use MusicSync\Service\FileOperation\Directory;

interface Progress
{
    public function scanDirectory(Directory $dir);

    public function getPercent(): int;
}
