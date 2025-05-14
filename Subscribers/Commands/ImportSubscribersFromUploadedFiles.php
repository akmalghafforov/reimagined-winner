<?php

namespace Subscribers\Commands;

use Files\Repositories\FileRepository;

class ImportSubscribersFromUploadedFiles
{
    public function __construct(private readonly FileRepository $fileRepository)
    {
    }

    public function handle()
    {
        $nextFile = $this->fileRepository->getNextForProcessing();
        if (empty($nextFile['id'])) {
            return;
        }

        //$this->fileRepository->updateStatus($nextFile['id'], FileStatusEnum::PROCESSING);
        $subscribersToImport = $this->parseCsvToArray($nextFile['path']);

        foreach (array_chunk($subscribersToImport, 100) as $chunk) {
            $numbers = array_column($chunk, 0);
            print_r($numbers);
            exit;
        }
    }

    private function parseCsvToArray(string $filePath, bool $withHeaders = false): array
    {
        $data = [];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $data;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = $withHeaders ? fgetcsv($handle) : [];

            while (($row = fgetcsv($handle)) !== false) {
                if ($withHeaders) {
                    $data[] = array_combine($headers, $row);
                } else {
                    $data[] = $row;
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
