<?php

namespace MusicSync\Test\Unit;

use PHPUnit\Framework\TestCase;
use MusicSync\Service\FileOperation\Link;
use Exception;

class LinkTest extends TestCase
{
    /**
     * Files with trailing directory separators are not allowed
     */
    public function testLinkWithTrailingSeparator()
    {
        try {
            $file = new Link(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR);
            $failed = false;
        }
        catch (Exception $e) {
            $failed = true;
        }

        $this->assertTrue($failed);
    }
}
