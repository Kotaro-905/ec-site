@extends('layouts.app')

@section('title', 'メール認証の手順')

@section('css')
<style>
    .verify-card {
        max-width: 520px;
        margin: 4rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 2rem 2.5rem;
        text-align: center;
    }

    .verify-card h1 {
        font-size: 1.5rem;
        font-weight: bold;
        color: #222;
        margin-bottom: 1rem;
    }

    .verify-card p {
        color: #333;
        line-height: 1.6;
        text-align: left;
    }

    .verify-card ol {
        text-align: left;
        color: #333;
        margin: 1rem 0;
        padding-left: 1.2rem;
    }

    .verify-btn {
        display: block;
        width: 100%;
        padding: 0.9rem;
        border-radius: 6px;
        font-weight: bold;
        margin-top: 1rem;
        text-decoration: none;
    }

    .verify-btn.green {
        background: #22c55e;
        color: #fff;
    }

    .verify-btn.gray {
        background: #e5e7eb;
        color: #111827;
    }

    .verify-btn.blue {
        background: #c7d2fe;
        color: #1e3a8a;
    }
</style>
@endsection

@section('content')
<div class="verify-card">
    <h1>メール認証の手順</h1>
    <p>ご登録いただいたメールに記載されたURLを開いて、メール認証を完了してください。ブラウザを切り替えた後も、この画面は開いたままで問題ありません。</p>

    <ol>
        <li>メール内の「メールアドレスを確認する」ボタン（または記載されたURL）をクリックします。</li>
        <li>ブラウザで認証が完了すると、自動的にログイン状態が更新されます。</li>
        <li>認証後は下のボタンから商品一覧画面に進めます。</li>
    </ol>

    {{-- 商品一覧へ進む --}}
    <form method="GET" action="{{ route('verification.check') }}">
        @csrf
        <button type="submit" class="verify-btn green">商品一覧へ進む</button>
    </form>

    {{-- 認証メール再送 --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-btn gray">認証メールを再送する</button>
    </form>

    {{-- ログアウト --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="verify-btn blue">ログアウト</button>
    </form>
</div>
@endsection