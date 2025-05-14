<?php

require_once __DIR__ . '/Core/Helpers/PdoHelper.php';
require_once __DIR__ . '/Files/Repositories/Interfaces/FileRepositoryInterface.php';
require_once __DIR__ . '/Files/Enums/FileStatusEnumEnum.php';
require_once __DIR__ . '/Files/Repositories/FileRepository.php';
require_once __DIR__ . '/Subscribers/Commands/ImportSubscribersFromUploadedFiles.php';

use Files\Repositories\FileRepository;
use Subscribers\Commands\ImportSubscribersFromUploadedFiles;

$argv = $_SERVER['argv'] ?? [];
$command = $argv[1] ?? '';
$commandMap = [
    'command:import-subscribers-from-uploaded-files' => function () {
        $repo = new FileRepository(PdoHelper::getConnection());
        return new ImportSubscribersFromUploadedFiles($repo);
    },
];

$commandClosure = $commandMap[$command] ?? null;
if (!$commandClosure) {
    echo 'The requested command does not exist.';
    exit;
}

$commandObj = $commandClosure();
$commandObj->handle();
echo PHP_EOL;
