@extends('layouts.app')

@section('title','商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}">
@endsection

@section('content')

@php $q = request('q'); @endphp

<div class="items__tabs">
  <a href="{{ route('items.index', ['tab' => 'recommend'] + request()->only('q')) }}"
     class="items__tab {{ $tab==='recommend' ? 'is-active' : '' }}">
    おすすめ
  </a>

  @auth
  <a href="{{ route('items.index', ['tab' => 'mylist'] + request()->only('q')) }}"
     class="items__tab {{ $tab==='mylist' ? 'is-active' : '' }}">
    マイリスト
  </a>
  @endauth

</div>

    <div class="items__grid">
        @forelse($items as $item)
        {{-- SOLD のときにクラスを付けておくと見た目調整が楽です --}}
        <article class="item {{ (int)$item->status === 2 ? 'is-sold' : '' }}">
            <a href="{{ route('items.show', $item) }}" class="item__link">
                {{-- サムネイル（相対配置にしてリボンを重ねる） --}}
                <div class="item__thumb item__thumb--wrap">
                    @if($item->image)
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                    @else
                    <div class="item__thumb--ph">商品画像</div>
                    @endif

                    {{-- SOLD リボン --}}
                    @if((int)$item->status === 2) {{-- ← SOLD の値に合わせて変更 --}}
                    <span class="item__sold">SOLD</span>
                    @endif
                </div>

                <h3 class="item__name">{{ $item->name }}</h3>
            </a>
        </article>
        @empty
        <p class="items__note">…表示できる商品がありません。</p>
        @endforelse
    </div>

   @if (method_exists($items, 'links'))
  <div class="items__pager">
    {{ $items->appends(request()->only('q','tab'))->links() }}
  </div>
   @endif
</div>
@endsection