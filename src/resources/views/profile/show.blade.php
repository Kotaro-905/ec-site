@extends('layouts.app')

@section('title', 'プロフィール')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
    {{-- ヘッダー帯 --}}
    <div class="profile__header">
        <div class="profile__avatar">
            @if($user->image)
            <img src="{{ asset('storage/'.$user->image) }}" alt="{{ $user->name }}">
            @else
            <div class="avatar__ph"></div>
            @endif
        </div>
        <div class="profile__meta">
            <h1 class="profile__name">{{ $user->name }}</h1>
        </div>
        <div class="profile__actions">
            <a href="{{ route('profile.edit', [], false) }}" class="profile__edit">プロフィールを編集</a>
        </div>
    </div>

    {{-- タブ（見た目だけ。今は出品した商品だけ表示） --}}
    <div class="profile__tabs">
        <span class="tab is-active">出品した商品</span>
        <span class="tab is-disabled">購入した商品</span>
    </div>

    {{-- 出品した商品グリッド --}}
    <div class="items__grid">
        @forelse($listedItems as $item)
        <article class="item">
            <a href="#" class="item__link">
                @if($item->image)
                <img class="item__thumb" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                @else
                <div class="item__thumb item__thumb--ph">商品画像</div>
                @endif
                <h3 class="item__name">{{ $item->name }}</h3>
                <div class="item__meta">
                    <span class="item__price">¥{{ number_format($item->price) }}</span>
                    {{-- カウントを出すなら
            <span class="item__count">★ {{ $item->likes_count }}</span>
                    <span class="item__count">💬 {{ $item->comments_count }}</span>
                    --}}
                </div>
            </a>
        </article>
        @empty
        <p class="profile__empty">…まだ出品がありません。</p>
        @endforelse
    </div>

    {{-- ページャ --}}
    @if($listedItems->hasPages())
    <div class="pager">
        {{ $listedItems->links() }}
    </div>
    @endif
</div>
@endsection