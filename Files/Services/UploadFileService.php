<?php

namespace Files\Services;

class UploadFileService
{
    public function isCsvFileBeingUploaded($file): bool
    {
        $fileName = $file['name'] ?? '';
        $fileTmpPath = $file['tmp_name'] ?? '';
        $fileType = mime_content_type($fileTmpPath);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $validMimeTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
        $validExtension = 'csv';

        return in_array($fileType, $validMimeTypes) && $fileExt === $validExtension;
    }

    public function saveFile($file): ?string
    {
        $uploadDir = __DIR__ . '/../../Storage/Files/';
        $newFileName = 'data_' . time() . '.csv';
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $newFileName;
        }

        return null;
    }

    public function getFileHashes($file): array
    {
        $fileContent = file_get_contents($file['tmp_name']);
        $sha256 = hash('sha256', $fileContent);
        $md5_hash = md5($fileContent);

        return [$md5_hash, $sha256];
    }
}