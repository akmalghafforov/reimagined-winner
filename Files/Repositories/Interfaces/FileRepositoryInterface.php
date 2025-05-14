<?php

namespace Files\Repositories\Interfaces;

interface FileRepositoryInterface
{
    public function checkIfTheFileWasNotUploadedPreviously($hashMd5, $hashSha256): bool;

    public function insert($name, $hashMd5, $hashSha256): bool;
}
