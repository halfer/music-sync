<?php

namespace MusicSync\Test\Unit;

use PHPUnit\Framework\TestCase;
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
