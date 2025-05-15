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
        $commandBuilder = $this->getCommandsBuilder();
        if (!$commandBuilder) {
            http_response_code(404);
            echo 'The requested resource does not exist.';
            exit;
        }

        $command = $commandBuilder();
        $command->run();
    }

    private function getCommandsBuilder(): callable
    {
        $request_uri = trim(explode('?', $_SERVER['REQUEST_URI'])[0] ?? '', '/');
        $request_method = $_SERVER['REQUEST_METHOD'];

        return match (true) {
            $request_uri === 'v1/files/upload' && $request_method === 'POST' => function () {
                return new UploadFileCommand(
                    new FilesRepository(PdoHelper::getConnection()),
                    new UploadFileService()
                );
            },
            default => null,
        };
    }
}