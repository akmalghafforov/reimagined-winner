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
                $sanitizedRow = array_map(function ($value) {
                    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                    $value = trim($value);

                    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }, $row);

                if ($withHeaders) {
                    $data[] = array_combine($headers, $sanitizedRow);
                } else {
                    $data[] = $sanitizedRow;
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
