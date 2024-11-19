<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($message,$data =null)
    {
        return response([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }


    public static function failed($message, $code= 400)
    {
        return response([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
