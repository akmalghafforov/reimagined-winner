<?php

namespace Files\Api\V1;

use Core\Traits\JsonResponse;
use Files\Repositories\Interfaces\FilesRepositoryInterface;
use Files\Services\UploadFileService;

class UploadFileCommand
{
    use JsonResponse;

    public function __construct(
        private readonly FilesRepositoryInterface $fileRepository,
        private readonly UploadFileService        $uploadService
    )
    {
    }

    public function run(): void
    {
        $file = $_FILES['file'] ?? null;
        if (empty($file)) {
            $this->sendJson(400, 'Please provide a file.');;
        }

        if (!$this->uploadService->isCsvFileBeingUploaded($file)) {
            $this->sendJson(422, 'Only CSV files are allowed, uploaded file is not a CSV file.');
        }

        [$hashMd5, $hashSha256] = $this->uploadService->getFileHashes($file);

        if (!$this->fileRepository->checkIfTheFileWasNotUploadedPreviously($hashMd5, $hashSha256)) {
            $this->sendJson(409, 'The file has been already uploaded.');;
        }

        $uploadedFileName = $this->uploadService->saveFile($file);
        if (
            !$uploadedFileName ||
            !$this->fileRepository->insert($uploadedFileName, $hashMd5, $hashSha256)
        ) {
            $this->sendJson(500, 'Unable to upload the file. Internal server error.');
        }

        $this->sendJson(200, 'File successfully uploaded.');
    }
}
