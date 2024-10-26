<?php

namespace App\common;

class ApiResponse
{

    static function sendResponse($status=200 ,$message = null, $data =[])
    {

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response,$status);

    }

}
