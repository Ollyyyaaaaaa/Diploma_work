<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function sendResponse(array $data = [], int $errorCode = null, int $httpCode = 200): JsonResponse
    {
        $error = [];

        if (! is_null($errorCode)) {

            $error = [
                'error' => [
                    'code' => $errorCode
                ]
            ];
        }
        $response = array_merge($error, $data);
        return response()->json($response, $httpCode);
    }
}
