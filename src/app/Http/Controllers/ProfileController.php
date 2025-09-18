<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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

            // ★DBには相対パス（avatars/xxx.jpg）で保存
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
        $user = $request->user()->fresh()->load('address');

        // セッションから出品ID
        $listedIds = array_values(array_unique(array_map(
            'intval',
            $request->session()->get('my_listed_item_ids', [])
        )));
        $listedItems = empty($listedIds)
            ? collect()
            : \App\Models\Item::whereIn('id', $listedIds)
            ->orderByRaw('FIELD(id,' . implode(',', $listedIds) . ')')
            ->get();

        // セッションから購入ID
        $purchasedIds = array_values(array_unique(array_map(
            'intval',
            $request->session()->get('my_purchased_item_ids', [])
        )));
        $purchasedItems = empty($purchasedIds)
            ? collect()
            : \App\Models\Item::whereIn('id', $purchasedIds)
            ->orderByRaw('FIELD(id,' . implode(',', $purchasedIds) . ')')
            ->get();

        return view('profile.show', [
            'user'            => $user,
            'listedItems'     => $listedItems,
            'purchasedItems'  => $purchasedItems,
            'tab'             => $request->query('tab', 'listed'), // デフォは出品した商品
        ]);
    }
}
