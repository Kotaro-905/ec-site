<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Address;
use App\Models\Item;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function checkout(PurchaseRequest $request, Item $item)
    {
        $data   = $request->validated();
        $method = $data['payment_method'];

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // Sinatra の YOUR_DOMAIN に相当（実行中のホスト＋ポートを使う）
        $origin     = $request->getSchemeAndHttpHost();
        $successUrl = $origin . route('purchase.success', [], false) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = $origin . route('purchase.cancel', [], false);

        $session = $stripe->checkout->sessions->create([
            'mode'                 => 'payment',
            'payment_method_types' => [$data['payment_method']], // 'card' | 'konbini'
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'unit_amount'  => (int) $item->price,
                    'product_data' => ['name' => $item->name],
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'metadata'    => [
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
            return redirect()->route('items.index')->with('error', '決済セッションが見つかりませんでした。');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId, []);
        } catch (\Throwable $e) {
            return redirect()->route('items.index')->with('error', '決済情報の取得に失敗しました。');
        }

        $itemId = (int) ($session->metadata->item_id ?? 0);
        $method = $session->payment_method_types[0] ?? ($session->metadata->method ?? 'card');
        if (!$itemId) {
            return redirect()->route('items.index')->with('error', '対象商品が特定できませんでした。');
        }

        DB::transaction(function () use ($request, $itemId, $method) {
            // 1) SOLD にする（2 = SOLD）
            $item = Item::lockForUpdate()->find($itemId);
            if ($item && (int)$item->status !== 2) {
                $item->status = 2;
                $item->save();
            }

            // 2) 購入履歴を保存（同じ item を二重で入れない軽いガード）
            $exists = OrderItem::where('user_id', $request->user()->id)
                ->where('item_id', $itemId)
                ->exists();

            if (!$exists) {
                OrderItem::create([
                    'user_id'        => $request->user()->id,
                    'item_id'        => $itemId,
                    'payment_method' => $method === 'konbini' ? 2 : 1,
                  ]);
            }
        });

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
    public function updateAddress(AddressRequest $request, Item $item)
    {
        $data = $request->validated(); // postal_code, address は必須
        // building は任意（未入力なら null）
        $building = $request->filled('building') ? $request->input('building') : null;

        Address::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'postal_code' => $data['postal_code'], // ハイフンそのまま
                'address'     => $data['address'],
                'building'    => $building,
            ]
        );

        return redirect()->route('purchase.create', $item)
                         ->with('status', '配送先住所を更新しました。');
    }
}
