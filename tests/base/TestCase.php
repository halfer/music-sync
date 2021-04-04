<?php

namespace MusicSync\Test;
use MusicSync\Service\FileOperation\Directory;
use MusicSync\Service\FileOperation\File;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Produces an array representation of a nested class structure (for testing)
     */
    protected function exploreDirectory(Directory $dir): array {
        $list = [];
        foreach ($dir->getContents() as $item) {
            $entry = ['name' => $item->getName(), ];
            if ($item instanceof Directory) {
                $entry['contents'] = $this->exploreDirectory($item);
            }
            if ($item instanceof File && $item->hasSize()) {
                $entry['size'] = $item->getSize();
            }
            $list[] = $entry;
        }

        return $list;
    }

    protected function getTestDir()
    {
        return realpath(__DIR__ . '/..');
    }
}
