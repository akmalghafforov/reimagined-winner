<?php

require_once __DIR__ . '/Core/Helpers/PdoHelper.php';
require_once __DIR__ . '/Core/Helpers/CsvHelper.php';
require_once __DIR__ . '/Files/Repositories/Interfaces/FilesRepositoryInterface.php';
require_once __DIR__ . '/Files/Enums/FileStatusEnumEnum.php';
require_once __DIR__ . '/Files/Repositories/FilesRepository.php';
require_once __DIR__ . '/Subscribers/Commands/ImportSubscribersFromUploadedFiles.php';
require_once __DIR__ . '/Subscribers/Repositories/Interfaces/SubscribersRepositoryInterface.php';
require_once __DIR__ . '/Subscribers/Repositories/SubscribersRepository.php';

use Core\Helpers\PdoHelper;
use Files\Repositories\FilesRepository;
use Subscribers\Repositories\SubscribersRepository;
use Subscribers\Commands\ImportSubscribersFromUploadedFiles;

$argv = $_SERVER['argv'] ?? [];
$command = $argv[1] ?? '';
$commandMap = [
    'command:import-subscribers-from-uploaded-files' => function () {
        $fileRepository = new FilesRepository(PdoHelper::getConnection());
        $subscriberRepository = new SubscribersRepository(PdoHelper::getConnection());

        return new ImportSubscribersFromUploadedFiles(
            $fileRepository,
            $subscriberRepository
        );
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
