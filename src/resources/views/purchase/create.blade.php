@extends('layouts.app')

@section('title', '商品購入')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">

    {{-- 左：詳細エリア --}}
    <section class="purchase__main">
        <div class="itemline">
            <div class="itemline__thumb">
                @if($item->image)
                <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                @else
                <div class="ph">商品画像</div>
                @endif
            </div>
            <div class="itemline__meta">
                <h1 class="itemline__title">{{ $item->name }}</h1>
                <div class="itemline__price">¥{{ number_format($item->price) }}</div>
            </div>
        </div>

        <hr class="sep">

        {{-- 支払い方法 --}}
        <div class="block">
            <h2 class="block__title">支払い方法</h2>
            <div class="field">
                <select name="payment_method" class="select" id="payment-method">
                    <option value="" selected disabled>選択してください</option>
                    <option value="card">クレジットカード</option>
                    <option value="konbini">コンビニ払い</option>
                    <option value="bank">銀行振込</option>
                </select>
            </div>
        </div>

        <hr class="sep">

        {{-- 配送先 --}}
        <div class="block">
            <h2 class="block__title">配送先</h2>
            <div class="address">
                @php
                $zip = $address?->postal_code ? '〒 '.chunk_split($address->postal_code, 3, '-') : '未設定';
                $addr = trim(($address->address ?? '').' '.($address->building ?? ''));
                @endphp
                <div class="address__text">
                    <div class="address__zip">{{ $zip }}</div>
                    <div class="address__body">{{ $addr ?: 'ここには住所と建物が入ります' }}</div>
                </div>
                <a class="address__edit" href="{{ route('profile.edit') }}">変更する</a>
            </div>
        </div>

        {{-- モバイル用ボタン --}}
        <button class="buybtn buybtn--mobile" type="button" onclick="alert('ダミー処理です');">購入する</button>
    </section>

    {{-- 右：サマリーカード --}}
    <aside class="purchase__summary">
        <div class="summary">
            <div class="summary__row">
                <div class="summary__label">商品代金</div>
                <div class="summary__value">¥{{ number_format($item->price) }}</div>
            </div>
            <div class="summary__row">
                <div class="summary__label">支払い方法</div>
                <div class="summary__badge" id="summary-method">未選択</div>
            </div>

            {{-- ダミー処理 --}}
            <button class="buybtn" type="button" onclick="alert('ダミー処理です');">購入する</button>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('payment-method');
        const label = document.getElementById('summary-method');
        const text = {
            card: 'クレジットカード',
            konbini: 'コンビニ払い',
            bank: '銀行振込'
        };
        if (sel) {
            sel.addEventListener('change', () => {
                label.textContent = text[sel.value] ?? '未選択';
            });
        }
    });
</script>
@endsection