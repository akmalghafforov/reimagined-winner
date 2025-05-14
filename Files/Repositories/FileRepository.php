<?php

namespace Files\Repositories;

use PDO;
use PDOException;

use Core\Enums\FileStatusEnum;
use Core\Repositories\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
            $path = 'Storage/Files/' . $name;
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
            return false;
        }
    }
}
