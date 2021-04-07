<?php

namespace MusicSync\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

abstract class Base extends Command
{
    protected string $homeDir;

    public function setHomeDir(string $homeDir)
    {
        $this->homeDir = $homeDir;
    }

    protected function getHomeDir(): string
    {
        if (!isset($this->homeDir)) {
            throw new RuntimeException(
                'Home directory needs to be set before it is used'
            );
        }
        return $this->homeDir;
    }
}
