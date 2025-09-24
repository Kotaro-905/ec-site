<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
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

    // 出品した商品（新しい順）
    $listedItems = Item::where('user_id', $user->id)
        ->latest()
        ->get();

    // 購入した商品（order_items 経由で item を一緒に）
    $purchasedItems = OrderItem::with('item')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    // 既存の Blade に合わせる
    return view('profile.show', [
        'listedItems'    => $listedItems,
        'purchasedItems' => $purchasedItems,
        
    ]);
}
}
