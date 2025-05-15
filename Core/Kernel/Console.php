<?php

namespace Core\Kernel;

use Core\Helpers\PdoHelper;
use Core\Traits\ConsoleTrait;
use Files\Repositories\FilesRepository;
use Mail\Repositories\MailsRepository;
use Mail\Services\SendMailToSubscriberService;
use Subscribers\Repositories\SubscribersRepository;
use BulkMailer\Commands\SendNextMailToAllSubscribersCommand;
use Subscribers\Commands\ImportSubscribersFromUploadedFilesCommand;

class Console
{
    use ConsoleTrait;

    public function run(): void
    {
        $commandBuilder = $this->getCommandsBuilder();
        if (!$commandBuilder) {
            $this->line('The requested command does not exist.');
            return;
        }

        $commandObj = $commandBuilder();
        $commandObj->handle();
        $this->line('Exit');
    }

    private function getCommandsBuilder(): ?callable
    {
        $command = $_SERVER['argv'][1] ?? '';
        $commandMap =  [
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

        return $commandMap[$command] ?? null;
    }
}
