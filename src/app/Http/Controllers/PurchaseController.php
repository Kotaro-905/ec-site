<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    /** 購入手続き画面（支払い方法選択・配送先の確認） */
    public function create(Item $item, Request $request)
    {
        $user    = $request->user();
        $address = $user->address ?? null; // プロフィール保存済みの住所（なければ null）

        return view('purchase.create', [
            'item'    => $item,
            'user'    => $user,
            'address' => $address,
        ]);
    }

    /** 購入画面表示 */
    public function show(Item $item)
    {
        $user    = Auth::user();
        $address = $user?->address;

       
        return view('purchase.create', [
            'item'    => $item,
            'user'    => $user,
            'address' => $address,
        ]);
    }

    /** 購入実行（Stripe Checkout セッション作成 → リダイレクト） */
    public function checkout(Request $request, Item $item)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:card,konbini'],
        ]);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // Sinatra の YOUR_DOMAIN に相当（実行中のホスト＋ポートを使う）
        $origin = $request->getSchemeAndHttpHost();              
        $successUrl = $origin . route('purchase.success', [], false) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = $origin . route('purchase.cancel',  [], false);

        
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => [$data['payment_method']], // 'card' | 'konbini'
            'line_items' => [[
                'price_data' => [
                    'currency'    => 'jpy',
                    'unit_amount' => (int) $item->price,
                    'product_data' => ['name' => $item->name],
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'metadata' => [
                'item_id' => (string) $item->id,
                'user_id' => (string) (auth()->id() ?? 0),
                'method'  => $data['payment_method'],
            ],
            'customer_email' => auth()->user()?->email,
        ]);

        return redirect()->away($session->url, 303);
    }

    /** 成功遷移（webhook なしで SOLD 反映 & セッションへ追加） */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('items.index')
                ->with('error', '決済セッションが見つかりませんでした。');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId, []);
        } catch (\Throwable $e) {
            return redirect()->route('items.index')
                ->with('error', '決済情報の取得に失敗しました。');
        }

        $itemId = (int) ($session->metadata->item_id ?? 0);
        if (!$itemId) {
            return redirect()->route('items.index')
                ->with('error', '対象商品が特定できませんでした。');
        }

        // 1) SOLD にする
        $item = Item::find($itemId);
        if ($item && (int)$item->status !== 2) {
            $item->status = 2; // ← SOLD の値
            $item->save();
        }

        // 2) 「購入した商品」ID をセッションに積む（重複除去しつつ先頭に）
        $purchased = $request->session()->get('my_purchased_item_ids', []);
        array_unshift($purchased, $itemId);
        $purchased = array_values(array_unique(array_map('intval', $purchased)));
        $request->session()->put('my_purchased_item_ids', $purchased);

        // 3) 一覧へ
        return redirect()->route('items.index')->with('status', '購入処理を完了しました。');
    }

    /** キャンセル遷移 */

    public function cancel()
    {
        return redirect()->route('items.index')->with('status', '購入処理をキャンセルしました。');
    }


    /** 配送先住所の編集画面（購入フロー用） */
    public function editAddress(Item $item)
    {
        $user    = Auth::user();
        $address = $user?->address; // null でもOK

        return view('purchase.address', [
            'item'    => $item,
            'address' => $address,
            'user'    => $user,
        ]);
    }

    /** 配送先住所の更新（購入フロー用） */
    public function updateAddress(Request $request, Item $item)
    {
        $validated = $request->validate([
            'postal_code' => ['nullable', 'string', 'max:16'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ]);

        // 郵便番号の-除去
        if (isset($validated['postal_code'])) {
            $validated['postal_code'] = str_replace('-', '', $validated['postal_code']);
        }

        Address::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'postal_code' => $validated['postal_code'] ?? null,
                'address'     => $validated['address'] ?? null,
                'building'    => $validated['building'] ?? null,
            ]
        );

        // 元の購入画面に戻る
        return redirect()
            ->route('purchase.create', $item)
            ->with('status', '配送先住所を更新しました。');
    }
}
