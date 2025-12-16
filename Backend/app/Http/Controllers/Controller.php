<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Return successful JSON response
     */
    protected function success($data = null, int $status = 200)
    {
        return response()->json([
            'ok' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Return error JSON response
     */
    protected function error(string $code, string $message, $details = null, int $status = 400)
    {
        return response()->json([
            'ok' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
