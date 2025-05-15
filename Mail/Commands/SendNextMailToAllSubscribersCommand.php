<?php

namespace BulkMailer\Commands;

use Core\Traits\ConsoleTrait;
use Mail\Enums\MailStatusEnum;
use Mail\Services\SendMailToSubscriberService;
use Mail\Repositories\Interfaces\MailsRepositoryInterface;
use Subscribers\Repositories\Interfaces\SubscribersRepositoryInterface;

class SendNextMailToAllSubscribersCommand
{
    use ConsoleTrait;

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
            $this->line("Nothing to send");
            return;
        }

        $totalSentMails = 0;
        $atLeastOneBatchFailed = false;

        $allSubscribers = $this->subscribersRepository->getAllSubscribersToSentMailTo($nextMail['id']);

        foreach (array_chunk($allSubscribers, 100) as $subscribers) {
            if ($this->sendMailToSubscriberService->sendMailToSubscribers($subscribers, $nextMail['content'])) {
                $this->mailsRepository->addSubscribersToSentMails($subscribers, $nextMail['id']);
                $totalSentMails += count(array_values($subscribers));
            } else {
                $atLeastOneBatchFailed = true;
            }
        }

        if ($atLeastOneBatchFailed) {
            $this->mailsRepository->updateStatus($nextMail['id'], MailStatusEnum::PARTIALLY_SEND);
        } else {
            $this->mailsRepository->updateStatus($nextMail['id'], MailStatusEnum::SENT);
        }

        $this->line("In total $totalSentMails subscribers received the mail.");
    }
}
