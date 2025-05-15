<?php

namespace Mail\Repositories\Interfaces;

use Mail\Enums\MailStatusEnum;

interface MailsRepositoryInterface
{
    public function getNextMail(): ?array;

    public function addSubscribersToSentMails(array $subscribers, int $mailId): bool;

    public function updateStatus(int $mailId, MailStatusEnum $status);
}
