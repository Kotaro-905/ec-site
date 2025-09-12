<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /** 購入手続き画面（支払い方法選択・配送先の確認） */
    public function create(Item $item, Request $request)
    {
        $user    = $request->user();
        $address = $user->address ?? null;   // プロフィールで保存している住所（なければ null）

        return view('purchase.create', [
            'item'    => $item,
            'user'    => $user,
            'address' => $address,
        ]);
    }

    /** “購入する” 押下 */
    public function store(Item $item, Request $request)
    {
        
    }

    /** 完了画面 */
    public function complete()
    {
        return view('purchase.complete');
    }
}
