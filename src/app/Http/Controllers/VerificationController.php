<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function check(Request $request)
    {
        // 認証済みでなければ verify.notice へ戻す
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 認証済みなら商品一覧へ
        return redirect()->route('items.index');
    }
}
