<?php

namespace Mail\Repositories;

use PDO;
use Throwable;
use Mail\Enums\MailStatusEnum;
use Mail\Repositories\Interfaces\MailsRepositoryInterface;

class MailsRepository implements MailsRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getNextMail(): ?array
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                SELECT 
                    * 
                FROM 
                    mails
                WHERE 
                    status IN (:notStarted, :partiallyProcessed)
                ORDER BY 
                    id
                FOR UPDATE SKIP LOCKED
                LIMIT 1
            ");
            $stmt->execute([
                'notStarted' => MailStatusEnum::NOT_STARTED->value,
                'partiallyProcessed' => MailStatusEnum::PARTIALLY_SEND->value,
            ]);
            $mail = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($mail) {
                // Step 2: Update status to 2
                $update = $this->pdo->prepare("
                    UPDATE 
                        mails 
                    SET 
                        status = :processingStatus 
                    WHERE id = :id"
                );
                $update->execute([
                    'processingStatus' => MailStatusEnum::PROCESSING->value,
                    'id' => $mail['id'],
                ]);
                $this->pdo->commit();

                $mail['status'] = MailStatusEnum::PROCESSING->value;

                return $mail;
            }

            $this->pdo->commit();

            return null;
        } catch (Throwable $e) {
            $pdo->rollBack();

            return null;
        }
    }

    public function addSubscribersToSentMails(array $subscribers, int $mailId): bool
    {
        if (empty($subscribers)) {
            return false;
        }

        $params = [];
        $placeholders = [];

        foreach ($subscribers as $index => $row) {
            $placeholders[] = "(:mail_id$index, :subscriber_id$index, NOW())";
            $params["mail_id$index"] = $mailId;
            $params["subscriber_id$index"] = $row['id'];
        }

        $sql = "INSERT INTO sent_mails (mail_id, subscriber_id, sent_at) VALUES " . implode(', ', $placeholders);
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function updateStatus(int $mailId, MailStatusEnum $status): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE 
                mails 
            SET 
                status = :status 
            WHERE 
                id = :id
        ");

        return $stmt->execute([
            ':id' => $mailId,
            ':status' => $status->value,
        ]);
    }
}
