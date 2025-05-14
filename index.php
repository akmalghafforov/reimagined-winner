<?php

require_once __DIR__ . '/Core/Helpers/PdoHelper.php';;
require_once __DIR__ . '/Core/Traits/JsonResponse.php';

require_once __DIR__ . '/Files/Repositories/Interfaces/FileRepositoryInterface.php';
require_once __DIR__ . '/Files/Enums/FileStatusEnumEnum.php';
require_once __DIR__ . '/Files/Repositories/FileRepository.php';
require_once __DIR__ . '/Files/Services/UploadFileService.php';
require_once __DIR__ . '/Files/Api/V1/UploadFileCommand.php';

use Files\Api\V1\UploadFileCommand;
use Files\Repositories\FileRepository;
use Files\Services\UploadFileService;

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($request_uri, '/v1/files/upload') && $request_method === 'POST') {
    $command = new UploadFileCommand(
        new FileRepository(PdoHelper::getConnection()),
        new UploadFileService()
    );
    $command->run();
} else {
    http_response_code(404);
    echo 'The requested resource does not exist.';
    exit;
}
