@extends('layouts.app')

@section('title','商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}">
@endsection

@section('content')
<div class="items">
    <div class="items__tabs">
        <a href="{{ route('items.index', ['tab'=>'recommend']) }}"
            class="items__tab {{ $tab==='recommend' ? 'is-active' : '' }}">おすすめ</a>
        <a href="{{ route('items.index', ['tab'=>'mylist']) }}"
            class="items__tab {{ $tab==='mylist' ? 'is-active' : '' }}">マイリスト</a>
    </div>

    @if($tab==='mylist' && !auth()->check())
    <p class="items__note">…ログインするとマイリストが見られます。</p>
    @endif

    <div class="items__grid">
        @forelse($items as $item)
        <article class="item">
            <a href="#" class="item__link" aria-disabled="true">
                {{-- サムネイル：正方形ラッパー --}}
                <div class="item__thumb">
                    @if($item->image)
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                    @else
                    <div class="item__thumb--ph">商品画像</div>
                    @endif
                </div>
                <h3 class="item__name">{{ $item->name }}</h3>
            </a>
        </article>
        @empty
        <p class="items__note">…表示できる商品がありません。</p>
        @endforelse
    </div>

    @if(method_exists($items,'links'))
    <div class="items__pager">{{ $items->withQueryString()->links() }}</div>
    @endif
</div>
@endsection