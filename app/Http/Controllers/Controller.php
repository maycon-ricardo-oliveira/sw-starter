<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function sendResponse(
        mixed $data,
        string $message,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
