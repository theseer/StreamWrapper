<?php

namespace TheSeer\Tools;

require __DIR__ . '/../autoload.php';

$mgr = new StreamManager();

$cfg = $mgr->register('example');
$cfg->baseDir = realpath(__DIR__.'/../src');

echo file_get_contents('example://StreamUri.php');