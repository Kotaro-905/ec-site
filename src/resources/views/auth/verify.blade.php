@extends('layouts.app')
@section('title', 'メール認証のお願い')

@section('content')
<div class="container" style="max-width:920px;margin:40px auto;">
  <div class="card" style="padding:40px 32px;text-align:center;">
    <h1 style="font-size:36px;margin-bottom:16px;">メール認証のお願い</h1>
    <p style="line-height:1.9;margin: 0 0 24px;">
      登録していただいたメールアドレス宛に認証メールを送付しました。<br>
      メール内のリンクをクリックして認証を完了してください。
    </p>

    <a href="{{ route('verification.confirm') }}" class="btn btn-primary" style="display:inline-block;padding:14px 28px;border-radius:8px;">
      認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}" style="margin-top:16px;">
      @csrf
      <button type="submit" class="btn btn-link">認証メールを再送する</button>
    </form>
  </div>
</div>
@endsection
