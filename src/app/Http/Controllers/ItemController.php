<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * 商品一覧（おすすめ / マイリスト）
     */
    public function index(Request $request)
    {
        if ($request->query('tab') === 'recommend') {
            // q があれば維持して / に正規化
            return redirect()->route('items.index', array_filter([
                'q' => $request->query('q'),
            ]));
        }

        $tab = $request->query('tab', 'recommend');
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
                $qq->where('name', 'like', "%{$q}%");
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
        $liked      = auth()->check()
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
        // フォームリクエストを使う場合
        $data = $request->validated();

        // 万一配列で来ても文字列に
        $comment = $data['comment'] ?? '';
        if (is_array($comment)) {
            $comment = implode('', $comment);
        }
        $comment = (string) $comment;

        Comment::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'comment' => $comment,
        ]);

        return back(); // 302 が返るのでテストの assertRedirect に一致
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
    public function store(ExhibitionRequest $request)
    {
        $v = $request->validated();

        $item              = new Item();
        $item->user_id     = $request->user()->id;
        $item->name        = $v['name'];
        $item->brand       = $v['brand'];
        $item->description = $v['description'];
        $item->price       = $v['price'];
        $item->condition   = $v['condition'];

        // 主カテゴリ：選択された最初の1件を採用
        $catIds            = collect($v['categories'])->map(fn($id) => (int)$id)->unique()->values();
        $item->category_id = $catIds->first();

        $item->status = 1;

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('items', 'public');
        }

        $item->save();

        // 多対多（item_categories）も使っている場合
        if (method_exists($item, 'categories')) {
            $item->categories()->sync($catIds);
        }

        return redirect()->route('profile.show')->with('status', '出品しました。');
    }
}
