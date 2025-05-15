<?php

namespace Core\Helpers;

use PDO;

class PdoHelper
{
    public static function getConnection(): PDO
    {
        $config = require __DIR__ . '/../Configs/db.php';

        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['db']};";
        $user = $config['user'];
        $password = $config['password'];

       return new PDO($dsn, $user, $password);
    }
}
