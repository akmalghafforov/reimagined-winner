<?php

namespace Core\Kernel;

use BulkMailer\Commands\SendNextMailToAllSubscribersCommand;
use Core\Helpers\PdoHelper;
use Files\Repositories\FilesRepository;
use Mail\Repositories\MailsRepository;
use Mail\Services\SendMailToSubscriberService;
use Subscribers\Commands\ImportSubscribersFromUploadedFilesCommand;
use Subscribers\Repositories\SubscribersRepository;

class Console
{
    public function run(): void
    {
        $argv = $_SERVER['argv'] ?? [];
        $command = $argv[1] ?? '';

        $commands = $this->getCommandsResolvers();
        $commandClosure = $commands[$command] ?? null;
        if (!$commandClosure) {
            echo 'The requested command does not exist.';
            exit;
        }

        $commandObj = $commandClosure();
        $commandObj->handle();
        echo PHP_EOL;
    }

    private function getCommandsResolvers(): array
    {
        return [
            'command:import-subscribers-from-uploaded-files' => function () {
                $fileRepository = new FilesRepository(PdoHelper::getConnection());
                $subscriberRepository = new SubscribersRepository(PdoHelper::getConnection());

                return new ImportSubscribersFromUploadedFilesCommand(
                    $fileRepository,
                    $subscriberRepository
                );
            },
            'command:send-next-mail-to-all-subscribers' => function () {
                $mailRepository = new MailsRepository(PdoHelper::getConnection());
                $subscriberRepository = new SubscribersRepository(PdoHelper::getConnection());

                return new SendNextMailToAllSubscribersCommand(
                    $mailRepository,
                    $subscriberRepository,
                    new SendMailToSubscriberService(),
                );
            }
        ];
    }
}
