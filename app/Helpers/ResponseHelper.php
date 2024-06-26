<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    public static function respondWithApi($message = null, $data = null, $status = Response::HTTP_OK)
    {
        $response = [];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public static function respondWithWeb($route, $message = null, $statusType = 'success')
    {
        $redirect = redirect()->route($route);

        if ($message) {
            $redirect->with($statusType, $message);
        }

        return $redirect;
    }
}
