<?php

namespace Files\Repositories\Interfaces;

use Files\Enums\FileStatusEnum;

interface FileRepositoryInterface
{
    public function checkIfTheFileWasNotUploadedPreviously($hashMd5, $hashSha256): bool;

    public function insert($name, $hashMd5, $hashSha256): bool;

    public function getNextForProcessing(): array;

    public function updateStatus(int $id, FileStatusEnum $status): bool;
}
