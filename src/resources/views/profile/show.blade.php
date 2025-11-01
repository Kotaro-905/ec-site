@extends('layouts.app')

@section('title', 'プロフィール')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
@php
// ?page=sell|buy（デフォルトは sell）
$page = $page ?? request('page', 'sell');
@endphp

<div class="profile">
  <div class="profile__header">
    <div class="profile__avatar">
      <img
        src="{{ $user->image ? asset('storage/'.$user->image) : asset('images/default-avatar.png') }}"
        alt="プロフィール画像"
        class="avatar__img">
    </div>
    <div class="profile__meta">
      <h1 class="profile__name">{{ $user->name ?? 'ユーザー名' }}</h1>
    </div>
    <div class="profile__actions">
      <a href="{{ route('profile.edit', [], false) }}" class="profile__edit">プロフィールを編集</a>
    </div>
  </div>

  <div class="profile__tabs">
    <a href="{{ route('profile.show', ['page' => 'sell'], false) }}"
      class="tab {{ $page === 'sell' ? 'is-active' : '' }}">出品した商品</a>
    <a href="{{ route('profile.show', ['page' => 'buy'], false) }}"
      class="tab {{ $page === 'buy' ? 'is-active' : '' }}">購入した商品</a>
  </div>

  <div class="items__grid">
    @if ($page === 'sell')
    @forelse ($listedItems as $item)
    @include('partials.item-card', ['item' => $item])
    @empty
    <p class="profile__empty">…まだ出品がありません。</p>
    @endforelse
    @else
    @forelse ($purchasedItems as $item)
    @include('partials.item-card', ['item' => $item])
    @empty
    <p class="profile__empty">…まだ購入履歴がありません。</p>
    @endforelse
    @endif
  </div>
</div>
@endsection