<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user    = auth()->user();
        $address = $user?->address;
        return view('profile.edit', compact('address'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // --- users 更新 ---
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        // 画像
        if ($request->hasFile('image')) {
            $path       = $request->file('image')->store('avatars', 'public');
            $user->image = Storage::url($path);
        }
        $user->save();

        // --- addresses upsert（空なら null/空文字で保存） ---
        $postal = $request->input('postal_code');
        if (is_string($postal)) {
            // ハイフン削除だけしておく
            $postal = str_replace('-', '', $postal);
        }

        Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $postal,
                'address'     => $request->input('address'),
                'building'    => $request->input('building'),
            ]
        );

        // 完了後に商品一覧へ
        return redirect()->route('items.index');
    }
}