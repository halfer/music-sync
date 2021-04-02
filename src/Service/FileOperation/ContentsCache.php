<?php

namespace MusicSync\Service\FileOperation;

class ContentsCache
{
    const LATEST_VERSION = '1';

    /**
     * Converts a directory contents to a storable format
     *
     * @param array $contents
     */
    public function serialise(array $contents)
    {
        $data = json_encode(
            $contents,
            JSON_PRETTY_PRINT
        );
        // Write a magic number to determine authenticity
        // Write a version number of this format

        return $data;
    }

    /**
     * Converts a storable format to directory contents
     *
     * @param string $data
     */
    public function deserialise(string $data)
    {
        // Blow up if the magic number is wrong
        // Blow us if the version number is too high
    }
}
