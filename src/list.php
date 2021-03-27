<?php

require_once 'common.php';

$files = listFolder('/home/jon/Music/Current Music/My Albums');
render($files);
