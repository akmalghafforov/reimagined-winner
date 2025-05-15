<?php

require_once __DIR__ . '/Helpers/PdoHelper.php';
require_once __DIR__ . '/Helpers/CsvHelper.php';
require_once __DIR__ . '/Traits/JsonResponse.php';
require_once __DIR__ . '/Kernel/Web.php';
require_once __DIR__ . '/Kernel/Console.php';

$dbConfig = require __DIR__ . '/Configs/db.php';