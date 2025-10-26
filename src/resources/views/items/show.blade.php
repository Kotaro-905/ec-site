@extends('layouts.app')

@section('title', $item->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/items-show.css') }}">
{{-- 簡易エラースタイル --}}
<style>
  .is-invalid { border: 1px solid #e74c3c; }
  .invalid-feedback { color:#e74c3c; font-size: .9rem; margin-top:.4rem; }
  .flash { background:#f5fff5; border:1px solid #c7eec7; color:#2d7a2d; padding:.6rem .8rem; border-radius:.4rem; margin-bottom:1rem; }
</style>
@endsection

@section('content')
<div class="show">
    <div class="show__grid">

        {{-- 左：画像 --}}
        <div class="show__image">
            @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
            @else
            <div class="ph">商品画像</div>
            @endif
        </div>

        {{-- 右：情報 --}}
        <div class="show__info">
        <h1 class="show__title">{{ $item->name }}</h1>
         <div class="show__brand">{{ $item->brand ?? 'ブランド名' }}</div>
         <div class="show__price">¥{{ number_format($item->price ?? 0) }} <small>(税込)</small></div>

         {{-- いいね＋コメント数（※今あるものをこのブロックにまとめる） --}}
           <div class="item-stats">
            <form method="POST" action="{{ route('items.like', $item) }}">
            @csrf
              <button type="submit" class="stat__icon {{ $liked ? 'is-liked' : '' }}" aria-label="like">
        {{ $liked ? '★' : '☆' }}
           </button>
              <div class="stat__num">{{ $likesCount }}</div>
          </form>

            <div class="stat">
              <button class="stat__icon" type="button" aria-disabled="true">💬</button>
            <div class="stat__num">{{ $item->comments->count() }}</div>
            </div>
            </div>

  {{-- ★ 商品説明の“直前”に購入ボタン（フル幅） --}}
            @php
             // 出品者自身、または既に購入済み（orderItemsが存在）なら購入不可にする
             $isOwner = auth()->check() && auth()->id() === $item->user_id;
             $isSold  = $item->orderItems->isNotEmpty();
           @endphp
           @if($isSold)
             <button class="main-btn is-sold" type="button" disabled aria-disabled="true">売り切れました</button>
           @elseif($isOwner)
             <button class="main-btn" type="button" disabled aria-disabled="true">購入できません</button>
           @else
             <a class="main-btn" href="{{ route('purchase.create', $item) }}">購入手続きへ</a>
           @endif

            {{-- 商品説明 --}}
            <h2 class="sec">商品説明</h2>
            <div class="show__desc">{!! nl2br(e($item->description ?? '')) !!}</div>

            {{-- 商品の情報 --}}
            <h2 class="sec">商品の情報</h2>
            <dl class="show__spec">
             <dt>カテゴリー</dt>
            <dd class="chips">
               @php
                // 1) 多対多があればそれを表示、無ければ主カテゴリ(category_id)を1つだけ表示
                $cats = $item->categories->isNotEmpty()
                ? $item->categories
                : collect(array_filter([$item->category])); // null を除去
               @endphp

                @forelse ($cats as $category)
                 <span class="chip">{{ $category->name }}</span>
                 @empty
                   -
                 @endforelse
                  </dd>

                 <dt>商品の状態</dt>
                  <dd>{{ $item->condition_label ?? '-' }}</dd>
                 </dl>

            {{-- コメント --}}
            <h2 class="sec">コメント({{ $item->comments->count() }})</h2>

@forelse($item->comments as $c)
    @php
        // プロフィール画像（storage/app/public/... に保存想定）
        $avatarUrl = ($c->user && $c->user->image)
            ? asset('storage/'.$c->user->image)
            : asset('images/default-avatar.png'); // プレースホルダー（無ければ用意）
    @endphp

    <div class="cmt-row">
        <img class="cmt-avatar" src="{{ $avatarUrl }}" alt="{{ $c->user->name ?? 'user' }}のアイコン">
        <div class="cmt-main">
            <div class="cmt-name">{{ $c->user->name ?? 'user' }}</div>
            <div class="cmt-bubble">{{ $c->comment }}</div>
        </div>
    </div>
@empty
    <p class="muted">まだコメントはありません。</p>
@endforelse

            {{-- コメント投稿フォーム（FormRequestバリデーション対応版） --}}
            @auth
            <div class="cmt-form">
                <label class="cmt-label">商品へのコメント</label>
                <form action="{{ route('items.comments.store', $item) }}" method="post">
                    @csrf
                    <textarea
                        name="comment"
                        rows="4"
                        placeholder="こちらにコメントを入力します。"
                        class="{{ $errors->has('comment') ? 'is-invalid' : '' }}"
                    >{{ old('comment') }}</textarea>

                    @error('comment')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="main-btn cmt-submit">コメントを送信する</button>
                </form>
            </div>
            @else
            <div class="cmt-form">
                {{-- ゲスト向け：ログインページへ（ログイン後に元ページへ戻すため redirect クエリを付与） --}}
                <a href="{{ route('login', ['redirect' => url()->full()]) }}" class="main-btn cmt-submit">
                    ログインしてコメントする
                </a>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection
