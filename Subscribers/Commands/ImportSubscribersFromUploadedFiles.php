<?php

namespace Subscribers\Commands;

use Throwable;
use Core\Helpers\CsvHelper;
use Files\Enums\FileStatusEnum;
use Files\Repositories\Interfaces\FilesRepositoryInterface;
use Subscribers\Repositories\Interfaces\SubscribersRepositoryInterface;

class ImportSubscribersFromUploadedFiles
{
    public function __construct(
        private readonly FilesRepositoryInterface       $fileRepository,
        private readonly SubscribersRepositoryInterface $subscribersRepository,
    ) {
    }

    public function handle(): void
    {
        $nextFile = $this->fileRepository->getNextForProcessing();
        if (empty($nextFile['id'])) {
            return;
        }

        echo "Import started." . PHP_EOL;

        $this->fileRepository->updateStatus($nextFile['id'], FileStatusEnum::PROCESSING);
        $subscribers = CsvHelper::parseCsvToArray($nextFile['path']);

        foreach (array_chunk($subscribers, 1000) as $index => $chunk) {
            try {
                $numbers = array_column($chunk, 0);
                $missingNumbers = $this->subscribersRepository->getMissingSubscriberNumbers($numbers);
                $missingSet = array_flip($missingNumbers);

                $subscribersToImport = array_filter($chunk, function ($subscriber) use ($missingSet) {
                    return isset($missingSet[(int)$subscriber[0]]);
                });

                $this->subscribersRepository->bulkInsert(
                    array_values($subscribersToImport)
                );

                echo "\tChunk #$index imported" . PHP_EOL;
            } catch (Throwable $e) {
                echo "\tChunk #$index failed" . PHP_EOL;
            }
        }

        $this->fileRepository->updateStatus($nextFile['id'], FileStatusEnum::COMPLETED);

        echo "Import finished." . PHP_EOL;
    }
}
