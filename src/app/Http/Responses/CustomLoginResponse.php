<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 初回（登録直後）ログイン時だけ true が入っている
        if ($request->session()->pull('must_setup_profile', false)) {
            return redirect()->route('profile.setup');
        }

        return redirect()->intended(route('items.index'));
    }
}