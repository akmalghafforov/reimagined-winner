<?php

namespace BulkMailer\Commands;

use Mail\Enums\MailStatusEnum;
use Mail\Repositories\Interfaces\MailsRepositoryInterface;
use Mail\Services\SendMailToSubscriberService;
use Subscribers\Repositories\Interfaces\SubscribersRepositoryInterface;

class SendNextMailToAllSubscribersCommand
{
    public function __construct(
        private readonly MailsRepositoryInterface       $mailsRepository,
        private readonly SubscribersRepositoryInterface $subscribersRepository,
        private readonly SendMailToSubscriberService    $sendMailToSubscriberService,
    ) {
    }

    public function handle()
    {
        $nextMail = $this->mailsRepository->getNextMail();
        if (empty($nextMail['id'])) {
            echo "Nothing to send" . PHP_EOL;
            return;
        }

        $count = 0;
        $failed = 0;

        $allSubscribers = $this->subscribersRepository->getAllSubscribersToSentMailTo($nextMail['id']);

        foreach (array_chunk($allSubscribers, 100) as $subscribers) {
            if ($this->sendMailToSubscriberService->sendMailToSubscribers($subscribers, $nextMail['content'])) {
                $this->mailsRepository->addSubscribersToSentMails($subscribers, $nextMail['id']);
                $count += count(array_values($subscribers));
            } else {
                $failed++;
            }
        }

        if ($failed > 0) {
            $this->mailsRepository->updateStatus($nextMail['id'], MailStatusEnum::PARTIALLY_SEND);
        } else {
            $this->mailsRepository->updateStatus($nextMail['id'], MailStatusEnum::SENT);
        }

        echo "In total $count subscribers received the mail." . PHP_EOL;
    }
}
