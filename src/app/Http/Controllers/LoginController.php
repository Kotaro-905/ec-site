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

        if (Auth::attempt($request->only('email', 'password'), true)) {
            $request->session()->regenerate();

            // まだメール認証していない場合は誘導ページへ
            if (! $request->user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // 認証済みなら商品一覧へ
            return redirect()->route('items.index');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function logout(LoginRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログアウト後はログイン画面へ
        return redirect()->route('login');
    }
}
