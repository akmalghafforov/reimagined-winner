<?php

namespace Subscribers\Repositories\Interfaces;

interface SubscribersRepositoryInterface
{
    public function getMissingSubscriberNumbers(array $numbers): array;

    public function bulkInsert(array $subscribers): bool;

    public function getAllSubscribersToSentMailTo(int $mailId): array;
}
