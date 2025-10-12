<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * 商品一覧（おすすめ / マイリスト）
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');
        $q   = trim((string) $request->input('q', ''));

        // ベースクエリ
        $items = Item::query();

        // 自分の出品を除外（「おすすめ」タブのときのみ）
        // ただし user_id が NULL のレコードは「他人扱い」として含める
        if ($tab !== 'mylist' && auth()->check()) {
            $items->where(function ($q2) {
                $q2->whereNull('items.user_id')
                   ->orWhere('items.user_id', '<>', auth()->id());
            });
        }

        // 検索条件（OR 検索は () でグルーピング）
        if ($q !== '') {
            $items->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('brand', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // マイリスト（いいねした商品のみ）
        if ($tab === 'mylist' && auth()->check()) {
            $likedItemIds = Like::where('user_id', auth()->id())
                ->pluck('item_id')
                ->all();

            // いいねが 0 件のときは空集合を返す
            if (empty($likedItemIds)) {
                $items->whereRaw('1 = 0');
            } else {
                $items->whereIn('items.id', $likedItemIds);
            }
        }

        // 並び順 & ページネーション（q, tab を維持）
        $items = $items->latest('items.id')
            ->paginate(12)
            ->appends($request->only('q', 'tab'));

        return view('items.index', compact('items', 'tab', 'q'));
    }

    /**
     * 検索フォームの送信：/items へリダイレクト（q, tab を載せる）
     */
    public function search(Request $request)
    {
        $q   = trim((string) $request->input('q', ''));
        $tab = $request->input('tab', 'recommend');

        return redirect()->route('items.index', array_filter([
            'q'   => $q,
            'tab' => $tab,
        ]));
    }

    /**
     * 商品詳細
     */
    public function show(Item $item)
{
    $item->load([
        'category',              // 主カテゴリ
        'categories',           
        'comments.user:id,name,image',
        'likes:id,user_id,item_id',
    ]);

    $likesCount = $item->likes->count();
    $liked = auth()->check()
        ? $item->likes->contains('user_id', auth()->id())
        : false;

    return view('items.show', compact('item', 'likesCount', 'liked'));
}

    /**
     * いいねのトグル
     */
    public function toggleLike(Request $request, Item $item)
    {
        $userId = $request->user()->id;

        $existing = $item->likes()->where('user_id', $userId)->first();
        if ($existing) {
            $existing->delete();
        } else {
            $item->likes()->create(['user_id' => $userId]);
        }

        return back();
    }

    /**
     * コメント投稿
     */
    public function storeComment(StoreCommentRequest $request, Item $item)
    {
        Comment::create([
            
        'user_id' => $request->user()->id,
        'item_id' => $item->id,
        'comment' => $request->validated('comment'), 
    ]);

         return back()->with('status', 'コメントを投稿しました。');
    }

    /**
     * 出品フォーム
     */
    public function create()
    {
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

    /**
     * 出品登録
     */
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
        $item->user_id     = $request->user()->id;
        $item->name        = $validated['name'];
        $item->brand       = $validated['brand'] ?? null;
        $item->description = $validated['description'] ?? null;
        $item->price       = $validated['price'];
        $item->condition   = $validated['condition'];
        $item->category_id = $validated['category_id'] ?? 1;
        $item->status      = 1; // 公開

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('items', 'public');
        }

        $item->save();

        return redirect()
            ->route('profile.show')
            ->with('status', '出品しました。');
    }
}
