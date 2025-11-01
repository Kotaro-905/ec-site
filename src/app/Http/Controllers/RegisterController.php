<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function create(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    protected function registered(Request $request, $user)
    {
        // 未認証ユーザーを「メール認証のお願い」画面へ誘導
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 認証済み（再登録など）なら通常ホームへ
        return redirect()->route('items.index');
    }
}
