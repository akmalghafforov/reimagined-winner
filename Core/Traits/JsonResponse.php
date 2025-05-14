<?php

namespace Core\Traits;

trait JsonResponse
{
    public function sendJson($code, $message, $data = []): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode([
            'error' => $message,
            'data' => $data,
            'code' => $code
        ]);
        exit;
    }
}
