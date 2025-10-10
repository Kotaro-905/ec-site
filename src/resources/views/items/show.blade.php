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

            {{-- 送信後メッセージ（任意） --}}
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            <div class="show__actions">
                {{-- いいね --}}
                <div class="likes">
                    <form method="POST" action="{{ route('items.like', $item) }}">
                        @csrf
                        <button type="submit" class="likes__btn {{ $liked ? 'is-liked' : '' }}">
                            {{ $liked ? '★' : '☆' }}
                        </button>
                        <span class="likes__count">{{ $likesCount }}</span>
                    </form>
                </div>

                {{-- 購入（ダミー） --}}
                <a class="buy-btn" href="{{ route('purchase.create', $item) }}">購入手続きへ</a>
            </div>

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
            <div class="cmt-row">
                <div class="cmt-avatar"></div>
                <div class="cmt-main">
                    <div class="cmt-name">{{ $c->user->name ?? 'user' }}</div>
                    {{-- 改行を保持して安全表示 --}}
                    <div class="cmt-bubble">{!! nl2br(e($c->comment)) !!}</div>
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

                    <button type="submit" class="cmt-submit">コメントを送信する</button>
                </form>
            </div>
            @else
            <p class="muted">コメントするにはログインしてください。</p>
            @endauth

        </div>
    </div>
</div>
@endsection
