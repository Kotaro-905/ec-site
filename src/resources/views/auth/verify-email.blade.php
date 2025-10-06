@extends('layouts.auth-header')

@section('title', 'メール認証のお願い')

@section('content')
<style>

.verify {
  max-width: 880px; margin: 60px auto; padding: 48px 32px;
  background: #fff; border-radius: 16px;
  box-shadow: 0 12px 32px rgba(0,0,0,.06);
  text-align: center;
}
.verify__title { font-size: 40px; font-weight: 800; margin-bottom: 24px; }
.verify__lead  { font-size: 20px; line-height: 1.9; color:#333; margin-bottom: 40px; }
.verify__btn   {
  display:inline-block; min-width: 280px; padding:16px 28px;
  background:#f85b5b; color:#fff; border:none; border-radius:10px;
  font-size:18px; font-weight:700; cursor:pointer;
}
.verify__link  {
  margin-top: 24px; display:inline-block; color:#1a73e8; font-weight:600;
  background:none; border:none; cursor:pointer; text-decoration:underline;
}
.verify__flash { margin:18px 0 0; color:#43a047; font-weight:700; }
</style>

<section class="card card--wide">
  <h1 class="title">メール認証のお願い</h1>
  <p>登録していただいたメールアドレス宛に認証メールを送付しました。メール内のリンクをクリックして認証を完了してください。</p>

  <div class="actions" style="margin-top:24px">
    {{-- ここを中間画面へ（GET遷移） --}}
    <a href="{{ route('verification.confirm') }}" class="btn-primary" style="display:inline-block;padding:.9rem 1.8rem;">
      認証はこちらから
    </a>
  </div>

  <form method="POST" action="{{ route('verification.send') }}" style="margin-top:12px">
      @csrf
      <button type="submit" class="link">認証メールを再送する</button>
  </form>
</section>
@endsection