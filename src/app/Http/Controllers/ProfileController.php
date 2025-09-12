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
        // 画面の表示に使う最新ユーザー（住所も取得）
        $user = $request->user()->fresh()->load('address');

        // セッションから出品IDを取り出し（重複除去・int化）
        $ids = array_values(array_unique(array_map(
            'intval',
            $request->session()->get('my_listed_item_ids', [])
        )));

        if (empty($ids)) {
            $items = collect(); // まだ出品なし
        } else {
            // セッション順に表示
            $idsList = implode(',', $ids);
            $items = Item::whereIn('id', $ids)
                ->orderByRaw("FIELD(id, $idsList)")
                ->get();
        }

        return view('profile.show', [
            'user'  => $user,
            'items' => $items,
        ]);
    }
}
