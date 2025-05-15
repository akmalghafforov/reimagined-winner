<?php

namespace Core\Kernel;

use Core\Helpers\PdoHelper;
use Files\Api\V1\UploadFileCommand;
use Files\Repositories\FilesRepository;
use Files\Services\UploadFileService;

class Web
{
    public function run(): void
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_method = $_SERVER['REQUEST_METHOD'];

        if (str_starts_with($request_uri, '/v1/files/upload') && $request_method === 'POST') {
            $command = new UploadFileCommand(
                new FilesRepository(PdoHelper::getConnection()),
                new UploadFileService()
            );
            $command->run();
            exit;
        }

        http_response_code(404);
        echo 'The requested resource does not exist.';
        exit;
    }
}