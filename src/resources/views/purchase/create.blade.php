@extends('layouts.app')

@section('title', '商品購入')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">
    <div class="purchase__container">

        {{-- 左カラム：商品詳細 + 支払い方法 + 配送先 --}}
        <div class="purchase__main">

            {{-- ヘッダーブロック（商品画像・商品名・価格） --}}
            <section class="purchase-head">
                <div class="purchase-head__thumb">
                    @if($item->image)
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                    @else
                    <div class="purchase-head__ph">商品画像</div>
                    @endif
                </div>
                <div class="purchase-head__meta">
                    <h1 class="purchase-head__name">{{ $item->name }}</h1>
                    <p class="purchase-head__price">¥{{ number_format($item->price) }}</p>
                </div>
            </section>

            <hr class="purchase__divider">

            {{-- 支払い方法（select） --}}
            <section class="purchase-block">
                <h2 class="purchase-block__title">支払い方法</h2>

                <form id="purchaseForm" class="purchase-form"
                    action="{{ route('purchase.checkout', $item) }}"
                    method="POST">
                    @csrf

                    <div class="purchase-form__row">
                        <div class="select-wrap">
                            <select name="payment_method" id="paymentMethod" class="select">
                                <option value="" selected disabled>選択してください</option>
                                <option value="konbini">コンビニ払い</option>
                                <option value="card">カード支払い</option>
                            </select>
                        </div>
                    </div>

                    {{-- 配送先 --}}
                    <div class="purchase__divider purchase__divider--thin"></div>

                    <h2 class="purchase-block__title">配送先</h2>
                    <div class="address">
                        @if($address)
                        <p class="address__line">〒 {{ $address->postal_code }}</p>
                        <p class="address__line">{{ $address->address }} {{ $address->building }}</p>
                        @else
                        <p class="address__line -muted">配送先が未登録です。プロフィールから登録してください。</p>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="address__edit">変更する</a>
                    </div>

                    {{-- 右カラムの購入ボタンと同じ動作にするため hidden submit は置かない --}}
                </form>
            </section>

            <div class="purchase__bottom-space"></div>
        </div>

        {{-- 右カラム：金額サマリ + 支払い方法表示 + 購入ボタン --}}
        <aside class="purchase__side">
            <div class="summary-card">

                <div class="summary-card__row">
                    <span class="summary-card__label">商品代金</span>
                    <span class="summary-card__value">¥{{ number_format($item->price) }}</span>
                </div>

                <div class="summary-card__row">
                    <span class="summary-card__label">支払い方法</span>
                    <span id="methodLabel" class="summary-card__value -muted">未選択</span>
                </div>

            </div>

            <button id="buyButton" class="purchase-button" disabled>
                購入する
            </button>

            {{-- バリデーション／フラッシュ --}}
            @if ($errors->any())
            <div class="purchase-alert -error">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="purchase-alert -error">{{ session('error') }}</div>
            @endif
            @if (session('status'))
            <div class="purchase-alert -ok">{{ session('status') }}</div>
            @endif
        </aside>

    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const methodSelect = document.getElementById('paymentMethod');
        const methodLabel = document.getElementById('methodLabel');
        const buyButton = document.getElementById('buyButton');
        const form = document.getElementById('purchaseForm');

        const labelMap = {
            konbini: 'コンビニ払い',
            card: 'カード支払い'
        };

        function updateUI() {
            const val = methodSelect.value;
            if (val && labelMap[val]) {
                methodLabel.textContent = labelMap[val];
                methodLabel.classList.remove('-muted');
                buyButton.disabled = false;
            } else {
                methodLabel.textContent = '未選択';
                methodLabel.classList.add('-muted');
                buyButton.disabled = true;
            }
        }

        methodSelect.addEventListener('change', updateUI);
        updateUI();

        // 右カラムの「購入する」ボタンでフォーム送信
        buyButton.addEventListener('click', function() {
            // 選択されていなければ送信しない（ガード）
            if (!methodSelect.value) return;
            form.submit();
        });
    })();
</script>
@endpush