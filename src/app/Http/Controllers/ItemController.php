<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // recommend | mylist

        if ($tab === 'mylist') {
            $items = auth()->check()
                ? Item::select('id', 'name', 'image', 'status')   // ← ここに status を追加
                ->whereHas('likes', fn($q) => $q->where('user_id', auth()->id()))
                ->latest()
                ->paginate(12)
                : collect();
        } else {
            $items = Item::select('id', 'name', 'image', 'status') // ← ここにも status を追加
                ->latest()
                ->paginate(12);
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


    //出品

    public function create()
    {
        // 画面のチップに使います
        $categories = Category::orderBy('name')->get();

        
        $conditions = [
            1 => '新品',
            2 => '未使用に近い',
            3 => '目立った傷や汚れなし',
            4 => 'やや傷や汚れあり',
            5 => '傷や汚れあり',
        ];

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image'        => ['nullable', 'image', 'max:4096'],
            'name'         => ['required', 'string', 'max:100'],
            'brand'        => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'price'        => ['required', 'integer', 'min:1', 'max:99999999'],
            'condition'    => ['required', 'integer', 'between:1,5'],
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $item = new Item();
        $item->name        = $validated['name'];
        $item->brand       = $validated['brand'] ?? null;
        $item->description = $validated['description'] ?? null;
        $item->price       = $validated['price'];
        $item->condition   = $validated['condition'];
        $item->category_id = $validated['category_id'] ?? 1;
        $item->status = 1;

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('items', 'public');
        }

        $item->save();

        // ① セッションに「自分が出品したID」を積む（新しいものを先頭へ）
        $ids = $request->session()->get('my_listed_item_ids', []);
        array_unshift($ids, (int) $item->id);        // 先頭に追加
        $ids = array_values(array_unique($ids));     // 重複排除
        $request->session()->put('my_listed_item_ids', $ids);

        // ② プロフィールへ遷移
        return redirect()->route('profile.show')->with('status', '出品しました。');
    }

}
