<?php

namespace Subscribers\Repositories\Interfaces;

use Generator;

interface SubscribersRepositoryInterface
{
    public function getMissingSubscriberNumbers(array $numbers): array;

    public function bulkInsert(array $subscribers): bool;

    public function getAllSubscribersToSentMailTo(int $mailId): array;
}
