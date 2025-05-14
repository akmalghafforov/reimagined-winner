<?php

namespace Core\Repositories;

interface FileRepositoryInterface
{
    public function checkIfTheFileWasNotUploadedPreviously($hashMd5, $hashSha256);

    public function insert($name, $hashMd5, $hashSha256): bool;
}
