<?php

namespace Subscribers\Commands;

use Throwable;
use Core\Helpers\CsvHelper;
use Core\Helpers\LogHelper;
use Core\Traits\ConsoleTrait;
use Files\Enums\FileStatusEnum;
use Files\Repositories\Interfaces\FilesRepositoryInterface;
use Subscribers\Repositories\Interfaces\SubscribersRepositoryInterface;

class ImportSubscribersFromUploadedFilesCommand
{
    use ConsoleTrait;

    public function __construct(
        private readonly FilesRepositoryInterface       $fileRepository,
        private readonly SubscribersRepositoryInterface $subscribersRepository,
    ) {
    }

    public function handle(): void
    {
        $nextFile = $this->fileRepository->getNextForProcessing();
        if (empty($nextFile['id'])) {
            $this->line('Nothing to import');
            return;
        }

        $this->line('Import started.');

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

                $this->line("\t Chunk #$index imported");
            } catch (Throwable $e) {
                LogHelper::log($e->getMessage());
                $this->line("\t Chunk #$index failed");
            }
        }

        $this->fileRepository->updateStatus($nextFile['id'], FileStatusEnum::COMPLETED);

        $this->line("Import finished.");
    }
}
