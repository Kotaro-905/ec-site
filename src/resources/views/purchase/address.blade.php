@extends('layouts.app')

@section('title', '住所の変更')

@section('css')
<style>
    .address-card {
        max-width: 800px;
        margin: 32px auto;
        padding: 24px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
    }

    .address-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 24px;
    }

    .field {
        margin: 16px 0;
    }

    .label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 16px;
    }

    .input.is-invalid {
        border-color: #f87171;
        background: #fef2f2;
    }

    .actions {
        margin-top: 28px;
        text-align: center;
    }

    .btn-primary {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 9999px;
        background: #ff6b6b;
        color: #fff;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    .hint {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
    }
</style>
@endsection

@section('content')
<div class="address-card">
    <h1 class="address-title">住所の変更</h1>

    {{-- バリデーションエラーの全体表示 --}}
    @if ($errors->any())
    <div class="hint" style="margin-bottom:16px;">
        入力内容に誤りがあります。確認してください。
    </div>
    @endif

    <form method="post" action="{{ route('purchase.address.update', $item) }}">
        @csrf
        @method('PUT')

        {{-- 郵便番号 --}}
        <div class="field">
            <label class="label" for="postal_code">郵便番号</label>
            <input id="postal_code" name="postal_code" type="text"
                class="input @error('postal_code') is-invalid @enderror"
                value="{{ old('postal_code', $address->postal_code ?? '') }}"
                placeholder="123-4567（ハイフン必須）">
            @error('postal_code')
            <div class="hint">{{ $message }}</div>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="field">
            <label class="label" for="address">住所</label>
            <input id="address" name="address" type="text"
                class="input @error('address') is-invalid @enderror"
                value="{{ old('address', $address->address ?? '') }}"
                placeholder="都道府県 市区町村 番地">
            @error('address')
            <div class="hint">{{ $message }}</div>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="field">
            <label class="label" for="building">建物名</label>
            <input id="building" name="building" type="text"
                class="input @error('building') is-invalid @enderror"
                value="{{ old('building', $address->building ?? '') }}"
                placeholder="建物名・部屋番号など">
            @error('building')
            <div class="hint">{{ $message }}</div>
            @enderror
        </div>

        <div class="actions">
            <button class="btn-primary" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection