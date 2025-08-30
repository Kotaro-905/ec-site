@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<section class="card card--wide" aria-labelledby="profile-title">
    <h1 id="profile-title" class="title">プロフィール設定</h1>

    <form class="form form--profile" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
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
        </div>

        <!-- User name -->
        <div class="field">
            <label class="label" for="name">ユーザー名</label>
            <input class="input" id="name" name="name" type="text"
                value="{{ old('name', auth()->user()->name ?? '') }}">
        </div>

        <!-- Postal code -->
        <div class="field">
            <label class="label" for="postal_code">郵便番号</label>
            <input class="input" id="postal_code" name="postal_code" type="text"
                value="{{ old('postal_code', $address->postal_code ?? '') }}">
        </div>

        <!-- Address -->
        <div class="field">
            <label class="label" for="address">住所</label>
            <input class="input" id="address" name="address" type="text"
                value="{{ old('address', $address->address ?? '') }}">
        </div>

        <!-- Building -->
        <div class="field">
            <label class="label" for="building">建物名</label>
            <input class="input" id="building" name="building" type="text"
                value="{{ old('building', $address->building ?? '') }}">
        </div>

        <div class="actions">
            <button class="btn-primary" type="submit">更新する</button>
        </div>
    </form>
</section>
@endsection