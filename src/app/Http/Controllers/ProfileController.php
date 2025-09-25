<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB; 
use App\Models\OrderItem;

class ProfileController extends Controller
{
    /** 編集画面 */
    

    public function edit()
    {
        $user = User::with('address')->findOrFail(auth()->id());

        return view('profile.edit', [
            'user'    => $user,
            'address' => $user->address,  // null の可能性あり
        ]);
    }

    /** 更新処理 */
    public function update(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'image'       => ['nullable', 'image', 'max:4096'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        // --- users 更新 ---
        $user->name = $request->input('name', $user->name);

        // 画像差し替え（/storage/app/public/avatars に保存）
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('avatars', 'public');

            // 以前の画像があれば削除（自分でアップしたもののみ対象）
            if ($user->image && str_starts_with($user->image, 'avatars/')) {
                Storage::disk('public')->delete($user->image);
            }

            
            $user->image = $newPath;
        }

        $user->save();

        // --- addresses を upsert ---
        $postal = $request->input('postal_code');
        $postal = is_string($postal) ? str_replace('-', '', $postal) : null;

        Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $postal,
                'address'     => $request->input('address'),
                'building'    => $request->input('building'),
            ]
        );

        // このリクエスト内で参照する場合の最新化
        $request->user()->refresh();

        // プロフィールへ戻る
        return redirect()->route('profile.show')->with('status', 'プロフィールを更新しました。');
    }

    /** プロフィール表示（出品リストはセッションのID順で） */
  public function show(Request $request)
{
    $user = $request->user();
    $tab  = $request->query('tab', 'listed');

    // 出品した商品（現状のまま）
    $listedItems = collect();
    $ids = array_values(array_unique(array_map('intval',
        $request->session()->get('my_listed_item_ids', [])
    )));
    if (!empty($ids)) {
        $listedItems = Item::whereIn('id', $ids)
            ->orderByRaw('FIELD(id, '.implode(',', $ids).')')
            ->get(['id','name','image','price','status']);
    }

    // 購入した商品：order_items → items の2段階（配列に確実にしてから検索）
    $purchasedIds = DB::table('order_items')
        ->where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->pluck('item_id')
        ->map(fn ($v) => (int) $v)   // 数値化
        ->values();

    if ($purchasedIds->isNotEmpty()) {
        $idArray = $purchasedIds->all();            // ← 配列にするのがポイント
        $csv     = implode(',', $idArray);          // FIELD() 用

        $purchasedItems = Item::query()
            ->whereIn('id', $idArray)
            ->when($csv !== '', fn ($q) => $q->orderByRaw("FIELD(id, $csv)"))
            ->get(['id','name','image','price','status']);
    } else {
        $purchasedItems = collect();
    }

    return view('profile.show', [
        'user'           => $user,
        'tab'            => $tab,
        'listedItems'    => $listedItems,
        'purchasedItems' => $purchasedItems,
    ]);
}
}