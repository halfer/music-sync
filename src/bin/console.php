<?php

$projectRoot = realpath(__DIR__ . '/../..');
require_once $projectRoot . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use MusicSync\Command\WriteContentsCache;

// Add the command list here
$classes = [
    WriteContentsCache::class,
];

// Add them to the application
$console = new Application();
$commands = [];
foreach ($classes as $class)
{
    $commands[] = new $class();
}

$console->addCommands($commands);
$console->run();