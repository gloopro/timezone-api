<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function sendApiResponse($status, $message, $data = null, $errors = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ], $status);
    }
}
