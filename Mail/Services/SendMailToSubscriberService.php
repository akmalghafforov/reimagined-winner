<?php

namespace Mail\Services;

use Mail\Enums\MailTypeEnum;

class SendMailToSubscriberService
{
    public function sendMailToSubscribers(
        $subscribers,
        $content,
        $mailType = MailTypeEnum::SMS
    ): bool {
        return true;
    }
}
