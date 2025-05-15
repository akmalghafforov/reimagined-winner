<?php

require_once __DIR__ . '/Core/autoload.php';
require_once __DIR__ . '/Files/autoload.php';

use Core\Kernel\Web;

$kernel = new Web();
$kernel->run();
