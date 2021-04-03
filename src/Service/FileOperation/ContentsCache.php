<?php

namespace MusicSync\Service\FileOperation;

use RuntimeException;

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
        /**
         * The magic number is a simple check to determine that the format was
         * written by this system.
         * The version number is handy to see if the data file is compatible
         * with this version of the system.
         */
        $data = [
            'name' => 'music-sync',
            'purpose' => 'Directory cache',
            'version' => self::LATEST_VERSION,
            'magic-number' => self::getMagicNumber(),
            'data' => $this->innerSerialise($contents),
        ];

        // FIXME we need to convert this recursively + manually

        return json_encode(
            $data,
            JSON_PRETTY_PRINT
        );
    }

    protected function innerSerialise(array $contents)
    {
        $data = [];

        foreach ($contents as $fsObject) {
            /* @var $fsObject FsObject */
            $dataObject = [
                'name' => $fsObject->getName(),
                'path' => $fsObject->getPath(),
                'type' => $this->getType($fsObject),
            ];
            if ($fsObject instanceof Directory)
            {
                $dataObject['contents'] = $this->innerSerialise(
                    $fsObject->getContents()
                );
            } else {
                /* @var $fsObject FileLike */
                $dataObject['size'] = $fsObject->getSize();
            }

            $data[] = $dataObject;
        }

        return $data;
    }

    protected function getType(FsObject $fsObject): string
    {
        switch (true) {
            case $fsObject instanceof Directory:
                return 'Directory';
            case $fsObject instanceof Link:
                return 'Link';
            case $fsObject instanceof File:
                return 'File';
            default:
                throw new RuntimeException('Type not recognised');
        }
    }

    /**
     * Converts a storable format to directory contents
     *
     * @param string $data
     */
    public function deserialise(string $data)
    {
        $decoded = json_decode($data, true);
        if (!$decoded) {
            throw new RuntimeException(
                'Data does not appear to be JSON'
            );
        }

        // Blow up if the magic number is wrong
        $magic = isset($decoded['magic-number']) ? $decoded['magic-number'] : null;
        if ($magic !== self::getMagicNumber()) {
            throw new RuntimeException(
                'Magic number is not set or is incorrect'
            );
        }

        // Blow us if the version number is too high
        $version = isset($decoded['version']) ? $decoded['version'] : null;
        if ($version > self::LATEST_VERSION) {
            throw new RuntimeException(
                'File version number is too high for this software version'
            );
        }

        // FIXME we need to rebuild this structure recursively + manually
    }

    /**
     * Returns false if the file version is not compatible with the software version
     *
     * (This will let us tolerate minor increments in the file version that an older
     * software version can still read).
     *
     * @param string $version
     */
    protected function isVersionCompatible(string $version): bool
    {
        // TODO
    }

    protected static function getMagicNumber()
    {
        return md5('music-sync');
    }
}
