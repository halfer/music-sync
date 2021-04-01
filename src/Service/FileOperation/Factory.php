<?php

namespace MusicSync\Service\FileOperation;

class Factory
{
    // The strategy to use when estimating progress in a recursive op
    const PROGRESS_ALGO_SIMPLE = 'simple';

    public function createFile(string $name, Directory $parent = null): File
    {
        return new File($name, $parent);
    }

    public function createDirectory(string $name, Directory $parent = null): Directory
    {
        return new Directory($name, $parent);
    }

    public function createProgressDevice(string $type)
    {
        switch ($type) {
            case self::PROGRESS_ALGO_SIMPLE:
                return $this->createSimpleProgressDevice();
                break;
        }
    }

    protected function createSimpleProgressDevice()
    {
        // TODO
    }
}
