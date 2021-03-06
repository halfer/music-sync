<?php

namespace MusicSync\Test\Unit;

use MusicSync\Test\TestCase;
use MusicSync\Service\FileOperation\File;
use Exception;

class FileTest extends TestCase
{
    public function testFileWithPath()
    {
        $file = new File(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b');
        $this->assertEquals('b', $file->getName(), 'Check file');
        $this->assertEquals(DIRECTORY_SEPARATOR . 'a', $file->getPath(), 'Check path');
    }

    public function testFileWithNoPath()
    {
        $file = new File('a');
        $this->assertEquals('a', $file->getName());
        $this->assertNull($file->getPath());
    }

    public function testFileEmptyNameRejected()
    {
        $pass = false;
        try {
            $file = new File('');
        }
        catch (\RuntimeException $e) {
            if ($e->getMessage() === 'An object name cannot be empty') {
                $pass = true;
            }
        }
        $this->assertTrue($pass, 'String names in this class cannot be empty');
    }

    public function testFileNullNameRejected()
    {
        $pass = false;
        try {
            $file = new File(null);
        }
        catch (\TypeError $e) {
            $pass = true;
        }
        $this->assertTrue($pass, 'Only string names are valid in this class');
    }

    /**
     * Files with trailing directory separators are not allowed
     */
    public function testFileWithTrailingSeparator()
    {
        try {
            $file = new File(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR);
            $failed = false;
        }
        catch (Exception $e) {
            $failed = true;
        }

        $this->assertTrue($failed);
    }

    public function testCannotFetchSizeIfItIsNotPopulated()
    {
        try {
            $failed = false;
            $file = new File('a');
            $file->getSize();
        }
        catch (\Exception $e) {
            $failed = true;
        }

        // The call needs to fail for this test to pass
        $this->assertTrue($failed);
    }
}
