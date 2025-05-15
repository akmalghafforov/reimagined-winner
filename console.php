<?php

require_once __DIR__ . '/Core/autoload.php';
require_once __DIR__ . '/Files/autoload.php';
require_once __DIR__ . '/Mail/autoload.php';
require_once __DIR__ . '/Subscribers/autoload.php';

use Core\Kernel\Console;

$kernel = new Console();
$kernel->run();
