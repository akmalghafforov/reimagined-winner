<?php

namespace Core\Helpers;

use RuntimeException;

class LogHelper
{
    public static function log(string $message): void
    {
        $today = date('Y-m-d');
        $timestamp = date('Y-m-d H:i:s');
        $logFileName = 'php_error_' . $today . '.log';

        $logDir = __DIR__ . '/../../Storage/App/Logs/';
        $logEntry = "[$timestamp] ERROR: {$message}" . PHP_EOL;

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
            throw new RuntimeException("Failed to create directory: $logDir");
        }

        if (!is_writable(dirname($logDir))) {
            throw new RuntimeException("Log directory is not writable.");
        }

        file_put_contents($logDir . $logFileName, $logEntry, FILE_APPEND | LOCK_EX);
   }
}