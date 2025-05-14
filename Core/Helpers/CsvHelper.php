<?php

namespace Core\Helpers;

class CsvHelper
{
    public static function parseCsvToArray(string $filePath, bool $withHeaders = false): array
    {
        $data = [];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $data;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = $withHeaders ? fgetcsv($handle) : [];

            while (($row = fgetcsv($handle)) !== false) {
                if ($withHeaders) {
                    $data[] = array_combine($headers, $row);
                } else {
                    $data[] = $row;
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
