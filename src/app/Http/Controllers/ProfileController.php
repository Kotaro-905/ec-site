<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\ProfileRequest;
use Symfony\Component\HttpKernel\Profiler\Profile;

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
    public function update(ProfileRequest $request)
    {
        $request->validated();
        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            // --- users 更新 ---
            $user->name = $request->input('name', $user->name);

            // 画像差し替え（/storage/app/public/avatars に保存）
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // 例: 5_20241001_193012_abcd12.jpg
                $filename = sprintf(
                    '%s_%s_%s.%s',
                    $user->id,
                    now()->format('Ymd_His'),
                    Str::random(6),
                    $file->getClientOriginalExtension()
                );

                // public ディスクに保存（=> public/storage/avatars/... で参照できる）
                $newPath = $file->storeAs('avatars', $filename, 'public');

                // 以前の画像があれば削除（自分がアップした avatars/ のみ）
                if ($user->image
                    && Str::startsWith($user->image, 'avatars/')
                    && Storage::disk('public')->exists($user->image)
                ) {
                    Storage::disk('public')->delete($user->image);
                }

                // DB には相対パス（例: avatars/xxx.jpg）を保存
                $user->image = $newPath;
            }

            $user->save();

            // --- addresses を upsert ---
            $postal = $request->input('postal_code');
            // ハイフン・空白を除去（数値だけに寄せる）
            $postal = is_string($postal) ? preg_replace('/[\s\-]/', '', $postal) : null;

            $data = $request->validated();

            Address::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'postal_code' => $data['postal_code'],
                    'address'     => $data['address'],
                    'building'    => $request->input('building'),
                ]
            );
        });

        // このリクエスト内で参照する場合の最新化
        $request->user()->refresh();

        return redirect()
            ->route('profile.show')
            ->with('status', 'プロフィールを更新しました。');
    }

    /** プロフィール表示（出品リストはセッションのID順で） */
    public function show(Request $request)
    {
        $user = $request->user();
        $tab  = $request->query('tab', 'listed');

        // ▼ 出品した商品（items.user_id で取得）
        $listedItems = Item::select('id', 'name', 'image', 'price', 'status')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

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
