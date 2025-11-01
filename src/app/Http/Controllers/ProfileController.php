<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Address;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /** 編集画面 */
    public function edit()
    {
        $user = User::with('address')->findOrFail(auth()->id());

        return view('profile.edit', [
            'user'    => $user,
            'address' => $user->address, // null の可能性あり
        ]);
    }

    /** 更新処理 */
    public function update(ProfileRequest $request)
    {
        $data = $request->validated();   // name / postal_code / address / (image, building など)

        $user = $request->user();

        DB::transaction(function () use ($request, $user, $data) {
            // --- users 更新 ---
            $user->name = $data['name'] ?? $user->name;

            // 画像差し替え（/storage/app/public/avatars）
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $filename = sprintf(
                    '%s_%s_%s.%s',
                    $user->id,
                    now()->format('Ymd_His'),
                    Str::random(6),
                    $file->getClientOriginalExtension()
                );

                $newPath = $file->storeAs('avatars', $filename, 'public');

                // 以前の画像があれば削除（自分の avatars/ のみ）
                if ($user->image
                    && Str::startsWith($user->image, 'avatars/')
                    && Storage::disk('public')->exists($user->image)
                ) {
                    Storage::disk('public')->delete($user->image);
                }

                $user->image = $newPath;
            }

            $user->save();

            // --- addresses を upsert ---
            Address::updateOrCreate(
                ['user_id' => $user->id],
                [
                    // ※ 郵便番号はハイフン必須のまま保存（FormRequestで検証）
                    'postal_code' => $data['postal_code'],
                    'address'     => $data['address'],
                    // 建物名は任意：未入力なら null を保存（DB 側は nullable に変更済み）
                    'building' => $request->filled('building') ? $request->input('building') : null,
                ]
            );
        });

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

        // 出品した商品
        $listedItems = Item::select('id', 'name', 'image', 'price', 'status')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 購入した商品
        $purchasedIds = DB::table('order_items')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('item_id')
            ->map(fn ($v) => (int) $v)
            ->values();

        if ($purchasedIds->isNotEmpty()) {
            $idArray = $purchasedIds->all();
            $csv     = implode(',', $idArray);

            $purchasedItems = Item::query()
                ->whereIn('id', $idArray)
                ->when($csv !== '', fn ($q) => $q->orderByRaw("FIELD(id, {$csv})"))
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
