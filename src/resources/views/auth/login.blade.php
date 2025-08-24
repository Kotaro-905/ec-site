<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ログイン画面</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <!-- ヘッダー -->
    <header class="header">
        <div class="header__bar">
            <div class="header__inner">
                <a href="{{ url('/') }}" class="header__logo">
                    <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="header__logo-img">
                </a>
            </div>
        </div>
    </header>

    <!-- 本文 -->
    <main class="page">
        <section class="card">
            <h1 class="title">ログイン</h1>

            <form action="{{ route('login') }}" method="post">
                @csrf

                <!-- メールアドレス -->
                <div class="field">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    @error('email')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- パスワード -->
                <div class="field">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password" required>
                    @error('password')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ボタン -->
                <div class="actions">
                    <button type="submit" class="btn-primary">ログインする</button>
                </div>

                <!-- リンク -->
                <p class="link">
                    <a href="{{ route('register') }}">会員登録はこちら</a>
                </p>
            </form>
        </section>
    </main>
</body>

</html>