<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // 出品した商品（新しい順）をページング
        $listedItems = $user->items()
            ->latest('id')
            ->withCount(['likes', 'comments'])   
            ->paginate(12);                      

        // 購入品タブを後で作るならここで取得
        // $purchasedItems = $user->orderItems()->with('item')->latest()->paginate(12);

        return view('profile.show', compact('user', 'listedItems'));
    }
}