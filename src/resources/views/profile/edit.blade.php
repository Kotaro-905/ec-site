@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<section class="card card--wide" aria-labelledby="profile-title">
    <h1 id="profile-title" class="title">プロフィール設定</h1>

    <form class="form form--profile" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="avatar">
            <div class="avatar__circle">
                {{-- ▼ プレビュー画像：users.image を優先。無ければデフォルト --}}
                <img
                  src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-avatar.png') }}"
                  alt="プロフィール画像">
            </div>
            <label class="avatar__button">
                画像を選択する
                <input type="file" name="image" accept="image/*" class="sr-only">
            </label>
        </div>

        <div class="field">
            <label class="label" for="name">ユーザー名</label>
            <input class="input" id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}">
        </div>

        <div class="field">
            <label class="label" for="postal_code">郵便番号</label>
            <input class="input" id="postal_code" name="postal_code" type="text"
                value="{{ old('postal_code', $user->address->postal_code ?? '') }}">
        </div>

        <div class="field">
            <label class="label" for="address">住所</label>
            <input class="input" id="address" name="address" type="text"
                value="{{ old('address', $user->address->address ?? '') }}">
        </div>

        <div class="field">
            <label class="label" for="building">建物名</label>
            <input class="input" id="building" name="building" type="text"
                value="{{ old('building', $user->address->building ?? '') }}">
        </div>

        <div class="actions">
            <button class="btn-primary" type="submit">更新する</button>
        </div>
    </form>
</section>
@endsection
