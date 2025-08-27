<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // recommend | mylist

        if ($tab === 'mylist') {
            // いいね商品（未ログイン時は空）
            $items = auth()->check()
                ? Item::select('id', 'name', 'image')
                ->whereHas('likes', fn($q) => $q->where('user_id', auth()->id()))
                ->latest()->paginate(12)
                : collect(); 
        } else {
            // おすすめ（ダミー：新着順）
            $items = Item::select('id', 'name', 'image')
                ->latest()->paginate(12);
        }

        return view('items.index', compact('items', 'tab'));
    }
}
