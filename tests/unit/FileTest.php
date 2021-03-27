<?php

namespace MusicSync\Test\Unit;

use PHPUnit\Framework\TestCase;
use MusicSync\Service\FileOperation\File;
use Exception;

class FileTest extends TestCase
{
    public function testFileCannotContainPath()
    {
        $fails = false;
        try {
            $file = new File('a' . DIRECTORY_SEPARATOR . 'b');
        }
        catch (Exception $e) {
            $fails = true;
        }
        $this->assertTrue($fails);
    }
}