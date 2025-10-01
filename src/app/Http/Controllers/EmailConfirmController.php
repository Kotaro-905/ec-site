<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class EmailConfirmController extends Controller
{
    // 誘導画面（「認証はこちらから」）
    public function notice()
    {
        return view('auth.verify-email');
    }

    // 中間画面（「認証する」ボタン）
    public function show()
    {
        return view('auth.verify');
    }

    // 「認証する」→ 署名URL生成 → 本家 verify へ
    public function perform()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // getEmailForVerification() が無いバージョンでも動くフォールバック
        $emailForHash = method_exists($user, 'getEmailForVerification')
            ? $user->getEmailForVerification()
            : $user->email;

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),           // ← Eloquent が必ず持つ
                'hash' => sha1($emailForHash),       // ← 標準の生成方法に合わせる
            ]
        );

        return redirect()->away($signedUrl);
    }
}