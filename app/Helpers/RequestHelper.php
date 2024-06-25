<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class RequestHelper
{
    public static function isApiRequest(Request $request)
    {
        return strpos($request->path(), 'api/') === 0;
    }

    public static function formatCpf($cpf)
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    public static function maskCpf($cpf)
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
}
