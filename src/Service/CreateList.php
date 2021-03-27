<?php

namespace MusicSync\Service;

// @todo These items will be removed once they are handled by the fileservice
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

class CreateList
{
    protected $fileService; // @todo Don't know the type of this one yet
    protected string $path;

    public function __construct($fileService, string $path)
    {
        $this->fileService = $fileService;
        $this->path = $path;
    }

    /**
     * @todo Use the fileservice rather than the PHP implementation
     * @return array
     */
    protected function listFolder(): array
    {
        $o_dir = new RecursiveDirectoryIterator($this->path);
        $o_dir->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
        $o_iter = new RecursiveIteratorIterator($o_dir);

        $list = [];
        foreach ($o_iter as $o_info) {
            // Clear the common path bit
            $relativePath = str_replace($path . '/', '', $o_info->getPathName());
            $list[] = [
                'pathname' => $relativePath,
                'size' => $o_info->getSize(),
            ];
        }

        usort($list, [$this, 'customSort']);

        return $list;
    }

    protected function customSort(array $a, array $b) {
        $aPath = $a['pathname'];
        $bPath = $b['pathname'];

        return $aPath <=> $bPath;
    }

    /**
     * @todo Change this to create an array of results
     * @param array $files
     */
    protected function render(array $files)
    {
        foreach ($files as $file) {
            echo sprintf(
                "% 6d\t%s\n",
                (int) ($file['size'] / 1024),
                $file['pathname']
            );
        }
    }

}