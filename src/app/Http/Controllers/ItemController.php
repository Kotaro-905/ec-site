<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
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


    public function search(Request $request)
    {
        $query = $request->input('q');  // 検索欄の入力値

        $items = Item::query()
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");  // 部分一致
            })
            ->paginate(12); // ページネーションも可

        return view('items.index', compact('items'))
            ->with('tab', 'recommend'); 
    }



    public function show(Item $item)
    {
        // まとめてロード（N+1防止）
        $item->load([
            'category',
            'comments.user:id,name,image', 
            'likes:id,user_id,item_id',
        ]);

        $likesCount = $item->likes->count();
        $liked = auth()->check()
            ? $item->likes->contains('user_id', auth()->id())
            : false;

        return view('items.show', compact('item', 'likesCount', 'liked'));
    }

    public function toggleLike(Request $request, Item $item)
    {
        $userId = $request->user()->id;

        // 既にいいね済みなら削除、無ければ作成
        $existing = $item->likes()->where('user_id', $userId)->first();
        if ($existing) {
            $existing->delete();
        } else {
            $item->likes()->create(['user_id' => $userId]);
        }

        return back();
    }

    public function storeComment(Request $request, Item $item)
    {
        Comment::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'comment' => $request->input('comment'),
        ]);

        return back();
    }
}


