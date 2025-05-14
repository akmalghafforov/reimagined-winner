<?php

namespace Core\Helpers;

use PDO;

class PdoHelper
{
    public static function getConnection(): PDO
    {
        $dsn = "pgsql:host=db;port=5432;dbname=app_db;";
        $user = "user";
        $password = "secret";

       return new PDO($dsn, $user, $password);
    }
}
