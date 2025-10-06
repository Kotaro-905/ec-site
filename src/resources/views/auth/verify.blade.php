@extends('layouts.auth-header')

@section('title','メール認証')

@section('content')
<section class="card card--wide">
  <h1 class="title">メール認証</h1>
  <p>下のボタンを押してメール認証を完了してください。</p>

  <form method="post" action="{{ route('verification.perform') }}">
    @csrf
    <button class="btn-primary">認証する</button>
  </form>
</section>
@endsection
