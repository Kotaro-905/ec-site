@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<section class="card card--wide" aria-labelledby="profile-title">
    <h1 id="profile-title" class="title">プロフィール設定</h1>

    <form class="form form--profile"  method="post" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <!-- Avatar -->
        <div class="avatar">
            <div class="avatar__circle">
                @if(auth()->user()->image ?? false)
                <img src="{{ auth()->user()->image }}" alt="プロフィール画像">
                @endif
            </div>
            <label class="avatar__button">
                画像を選択する
                <input type="file" name="image" accept="image/*" class="sr-only">
            </label>
            @error('image')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- User name -->
        <div class="field">
            <label class="label" for="name">ユーザー名</label>
            <input class="input" id="name" name="name" type="text"
                value="{{ old('name', auth()->user()->name ?? '') }}">
            @error('name')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Postal code -->
        <div class="field">
            <label class="label" for="postal_code">郵便番号</label>
            <input class="input" id="postal_code" name="postal_code" type="text"
                inputmode="numeric" pattern="[0-9\-]*"
                value="{{ old('postal_code', $address->postal_code ?? '') }}">
            @error('postal_code')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div class="field">
            <label class="label" for="address">住所</label>
            <input class="input" id="address" name="address" type="text"
                value="{{ old('address', $address->address ?? '') }}">
            @error('address')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Building -->
        <div class="field">
            <label class="label" for="building">建物名</label>
            <input class="input" id="building" name="building" type="text"
                value="{{ old('building', $address->building ?? '') }}">
            @error('building')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <button class="btn-primary" type="submit">更新する</button>
        </div>
    </form>
</section>
@endsection