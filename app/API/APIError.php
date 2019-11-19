<?php


namespace App\API;


class APIError
{
    public static function errorMessage($message, $code) {
        return [
            'data' => [
                'error' => $message,
                'code' => $code
            ]
        ];
    }
}