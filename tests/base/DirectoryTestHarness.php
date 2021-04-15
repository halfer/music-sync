<?php

namespace MusicSync\Test;

use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\FsObject;

/**
 * Not everyone likes this form of testing - tampering with the public
 * nature of methods to expose inner workings. But I think it is pretty
 * harmless in this case.
 */
class DirectoryTestHarness extends Directory
{
    public function pushObjectPublic(FsObject $object)
    {
        $this->contents[] = $object;
        $this->setPopulated();
    }
}
