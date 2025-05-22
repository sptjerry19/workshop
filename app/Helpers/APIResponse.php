<?php

namespace App\Helpers;

class APIResponse
{
    public static function success($data = [], $message = 'Success', $status = 200, $pagination = null)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $status);
    }

    public static function error($message = 'Error', $status = 500, $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data
        ], $status);
    }
}
