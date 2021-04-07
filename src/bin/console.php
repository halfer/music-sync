<?php

$projectRoot = realpath(__DIR__ . '/../..');
require_once $projectRoot . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use MusicSync\Command\WriteContentsCache;

// Add the command list here
$classes = [
    WriteContentsCache::class,
];

// Get env vars etc here
$homeDir = getenv('HOME');

// Add them to the application
$console = new Application();
$commands = [];
foreach ($classes as $class)
{
    $instance = new $class();
    /* @var $instance \MusicSync\Command\Base */
    $instance->setHomeDir($homeDir);
    $commands[] = $instance;
}

$console->addCommands($commands);
$console->run();