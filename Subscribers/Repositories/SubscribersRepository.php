<?php

namespace Subscribers\Repositories;

use Generator;
use MongoDB\BSON\Timestamp;
use PDO;
use Subscribers\Repositories\Interfaces\SubscribersRepositoryInterface;

class SubscribersRepository implements SubscribersRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getMissingSubscriberNumbers(array $numbers): array
    {
        if (empty($numbers)) {
            return [];
        }

        $pgList = '{' . implode(',', $numbers) . '}';
        $sql = "
            SELECT 
                nums.id 
            FROM 
                UNNEST(:ids::bigint[]) AS nums(id) 
            WHERE 
                NOT EXISTS (
                    SELECT 
                        1 
                    FROM 
                        subscribers 
                    WHERE subscribers.number = nums.id
                )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam('ids', $pgList);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function bulkInsert(array $subscribers): bool
    {
        if (empty($subscribers)) {
            return false;
        }

        $values = [];
        $placeholders = [];

        foreach ($subscribers as $index => [$number, $name]) {
            $placeholders[] = "(:name$index, :number$index, NOW())";
            $values["name$index"] = $name;
            $values["number$index"] = $number;
        }

        $sql = "INSERT INTO subscribers (name, number, created_at) VALUES " . implode(', ', $placeholders);
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($values);
    }

    public function getAllSubscribersToSentMailTo(int $mailId): array
    {
        $stmt = $this->pdo->prepare("
                SELECT 
                    s.*
                FROM 
                    subscribers s
                WHERE NOT EXISTS (
                    SELECT 
                        1
                    FROM 
                        sent_mails sm
                    WHERE 
                        sm.mail_id = :mail_id AND 
                        sm.subscriber_id = s.id
                )
                ORDER BY 
                    s.id
        ");

        $stmt->bindValue(':mail_id', $mailId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) {
            return [];
        }

        return $rows;
    }
}
