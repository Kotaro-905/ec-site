<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class CustomRegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        
        $request->session()->put('must_setup_profile', true);

        
        return redirect()->route('profile.setup');
    }
}
