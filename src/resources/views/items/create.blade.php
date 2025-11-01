@extends('layouts.app')

@section('title', '商品の出品')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items-create.css') }}">
@endsection

@section('content')
<div class="sell">
    <h1 class="sell__title">商品の出品</h1>

    {{-- フラッシュ/エラー（全体） --}}
    @if ($errors->any())
    <div class="sell__alert">
        <ul>
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('items.store') }}" method="post" enctype="multipart/form-data" class="sell__form">
        @csrf

        {{-- 画像 --}}
        <section class="sell__block">
            <h2 class="sell__head">商品画像</h2>
            <label class="imguploader">
                <input type="file" name="image" accept="image/*" id="imageInput" hidden>
                <div class="imguploader__box" id="imageBox">
                    <span class="imguploader__btn">画像を選択する</span>
                    <img id="imagePreview" alt="" style="display:none;">
                </div>
            </label>
            <p class="sell__note">※ 1枚まで。4MB以内の画像。</p>
            @error('image') <div class="hint">{{ $message }}</div> @enderror
        </section>

        {{-- 詳細 --}}
        <section class="sell__block">
            <h2 class="sell__head">商品の詳細</h2>

            {{-- カテゴリー（タグ風トグル） --}}
            <div class="sell__row">
                <label class="sell__label">カテゴリー</label>
                <div class="chips {{ $errors->has('categories') ? 'is-invalid' : '' }}">
                    @foreach($categories as $cat)
                    <label class="chip">
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $cat->id }}"
                            {{ in_array($cat->id, (array) old('categories', [])) ? 'checked' : '' }}
                            hidden>
                        <span>{{ $cat->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('categories') <div class="hint">{{ $message }}</div> @enderror
            </div>

            {{-- 状態 --}}
            <div class="sell__row">
                <label for="condition" class="sell__label">商品の状態</label>
                <select id="condition" name="condition"
                    class="sell__select {{ $errors->has('condition') ? 'is-invalid' : '' }}">
                    <option value="" hidden>選択してください</option>
                    @foreach($conditions as $key => $label)
                    <option value="{{ $key }}" @selected(old('condition')==$key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('condition') <div class="hint">{{ $message }}</div> @enderror
            </div>
        </section>

        {{-- テキスト群 --}}
        <section class="sell__block">
            <h2 class="sell__head">商品名と説明</h2>

            <div class="sell__row">
                <label class="sell__label" for="name">商品名</label>
                <input type="text" id="name" name="name"
                    value="{{ old('name') }}"
                    class="sell__input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="商品名を入力">
                @error('name') <div class="hint">{{ $message }}</div> @enderror
            </div>

            <div class="sell__row">
                <label class="sell__label" for="brand">ブランド名</label>
                <input type="text" id="brand" name="brand"
                    value="{{ old('brand') }}"
                    class="sell__input"
                    placeholder="ブランド名（任意）">
            </div>

            <div class="sell__row">
                <label class="sell__label" for="description">商品の説明</label>
                <textarea id="description" name="description" rows="4"
                    class="sell__textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                    placeholder="状態や付属品・サイズ感など">{{ old('description') }}</textarea>
                @error('description') <div class="hint">{{ $message }}</div> @enderror
            </div>

            <div class="sell__row">
                <label class="sell__label" for="price">販売価格</label>
                <div class="sell__price">
                    <span>¥</span>
                    <input type="number" id="price" name="price"
                        value="{{ old('price') }}"
                        class="sell__input {{ $errors->has('price') ? 'is-invalid' : '' }}"
                        min="1" step="1">
                </div>
                @error('price') <div class="hint">{{ $message }}</div> @enderror
            </div>
        </section>

        {{-- 送信 --}}
        <div class="sell__actions">
            <button type="submit" class="sell__submit">出品する</button>
        </div>
    </form>
</div>

{{-- 画像プレビュー & チップトグル --}}
<script>
    const input = document.getElementById('imageInput');
    const img = document.getElementById('imagePreview');
    const box = document.getElementById('imageBox');

    box.addEventListener('click', () => input.click());
    input.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        img.src = url;
        img.style.display = 'block';
        box.querySelector('.imguploader__btn')?.classList.add('is-hidden');
    });

    document.querySelectorAll('.chip').forEach(chip => {
        const checkbox = chip.querySelector('input[type="checkbox"]');
        const update = () => {
            chip.classList.toggle('is-on', checkbox.checked);
        };
        update();
        checkbox.addEventListener('change', update);
    });
</script>
@endsection