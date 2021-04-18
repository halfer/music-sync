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
    public function pushObjectPublic(FsObject $fsObject)
    {
        $this->contents[] = $fsObject;
        $this->setPopulated();

        // Point directories to their parent dir where possible
        if ($fsObject instanceof Directory) {
            $fsObject->setParent($this);
        }
    }

    public function pushObjects(array $fsObjects)
    {
        foreach ($fsObjects as $fsObject) {
            $this->pushObjectPublic($fsObject);
        }
    }
}
