<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login'); // 既存のログイン Blade
    }

    public function authenticate(LoginRequest $request)
    {
        
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, false)) {
            // 2. 入力情報が誤っている場合の共通メッセージ
            return back()
                ->withErrors(['login' => 'ログイン情報が登録されていません'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        return redirect()->intended(route('items.index'));
    }
}
