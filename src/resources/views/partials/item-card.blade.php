{{-- resources/views/partials/item-card.blade.php --}}
<article class="item">
    <a href="{{ route('items.show', $item) }}" class="item__link">
        <div class="item__thumb">
            @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
            @else
            <div class="item__thumb--ph">商品画像</div>
            @endif

            @if((int)$item->status === 2 && ($tab ?? null) !== 'purchased')
            <span class="item__sold">SOLD</span>
            @endif
        </div>
        <h3 class="item__name">{{ $item->name }}</h3>
    </a>
</article>