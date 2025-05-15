<?php

require_once __DIR__ . '/Core/autoload.php';
require_once __DIR__ . '/Files/autoload.php';
require_once __DIR__ . '/Mail/autoload.php';
require_once __DIR__ . '/Subscribers/autoload.php';

use Core\Helpers\PdoHelper;
use Files\Repositories\FilesRepository;
use Mail\Repositories\MailsRepository;
use Mail\Services\SendMailToSubscriberService;
use Subscribers\Repositories\SubscribersRepository;
use BulkMailer\Commands\SendNextMailToAllSubscribersCommand;
use Subscribers\Commands\ImportSubscribersFromUploadedFilesCommand;

$argv = $_SERVER['argv'] ?? [];
$command = $argv[1] ?? '';
$commandMap = [
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

$commandClosure = $commandMap[$command] ?? null;
if (!$commandClosure) {
    echo 'The requested command does not exist.';
    exit;
}

$commandObj = $commandClosure();
$commandObj->handle();
echo PHP_EOL;
