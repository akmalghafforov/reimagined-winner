<?php

namespace Files\Repositories;

use PDO;
use Throwable;
use PDOException;
use Core\Helpers\LogHelper;
use Files\Enums\FileStatusEnum;
use Files\Repositories\Interfaces\FilesRepositoryInterface;

class FilesRepository implements FilesRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function checkIfTheFileWasNotUploadedPreviously($hashMd5, $hashSha256): bool
    {
        try {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $this->pdo->prepare("select * from files where hash_md5 = ? and hash_sha256 = ?");
            $stmt->execute([$hashMd5, $hashSha256]);
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

            return empty($rows);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insert($name, $hashMd5, $hashSha256): bool
    {
        try {
            $path = 'Storage/Files/' . $name;// refactor
            $status = FileStatusEnum::NOT_STARTED->value;

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $this->pdo->prepare("
                INSERT INTO files (name, path, status, uploaded_at, hash_md5, hash_sha256) 
                VALUES (:name, :path, :status, NOW(), :hash_md5, :hash_sha256)
            ");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':path', $path);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':hash_md5', $hashMd5);
            $stmt->bindParam(':hash_sha256', $hashSha256);

            return $stmt->execute();
        } catch (PDOException $e) {
            LogHelper::log($e->getMessage());

            return false;
        }
    }

    public function getNextForProcessing(): ?array
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                SELECT 
                    *
                FROM 
                    files
                WHERE 
                    status = :notStarted OR (status = :processing and last_processed_at < NOW() - INTERVAL '24 hours')
                ORDER BY 
                    id
                FOR UPDATE SKIP LOCKED
                LIMIT 1
            ");
            $stmt->execute([
                'notStarted' => FileStatusEnum::NOT_STARTED->value,
                'processing' => FileStatusEnum::PROCESSING->value,
            ]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($file)) {
                $this->pdo->rollBack();

                return null;
            }

            $update = $this->pdo->prepare("
                UPDATE 
                    files
                SET status = :processing, last_processed_at = NOW()
                WHERE id = :fileId
            ");
            $update->execute([
                'processing' => FileStatusEnum::PROCESSING->value,
                'fileId' => $file['id'],
            ]);
            $this->pdo->commit();

            return $file;
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return null;
        }
    }

    public function updateStatus(int $id, FileStatusEnum $status): bool
    {
        try {
            $status = $status->value;
            $completedStatus = FileStatusEnum::COMPLETED->value;

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $this->pdo->prepare("
                UPDATE 
                    files
                SET 
                    status = :status,
                    last_processed_at = NOW()
                WHERE 
                    id = :id AND
                    status != :completed;
            ");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':completed', $completedStatus);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
