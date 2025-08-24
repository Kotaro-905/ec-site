<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        // ログアウト後にログイン画面へ
        return redirect()->route('login'); // もしくは redirect('/login')
    }
}