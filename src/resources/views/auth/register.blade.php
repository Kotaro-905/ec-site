<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body class="page">
    <div class="header__bar">
        <div class="header__inner">
            <a href="{{ url('/') }}" class="header__logo" aria-label="COACHTECH">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="header__logo-img">
            </a>
        </div>
    </div>

    <main class="auth">
        <div class="auth__container">
            <h1 class="auth__title">会員登録</h1>

            <form class="form" method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                <div class="form__group">
                    <label for="name" class="form__label">ユーザー名</label>
                    <input id="name" type="text" name="name" class="form__input"
                        value="{{ old('name') }}" required>
                    @error('name')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="email" class="form__label">メールアドレス</label>
                    <input id="email" type="email" name="email" class="form__input"
                        value="{{ old('email') }}" required>
                    @error('email')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="password" class="form__label">パスワード</label>
                    <input id="password" type="password" name="password" class="form__input"
                        required>
                    @error('password')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="password_confirmation" class="form__label">確認用パスワード</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form__input" required>
                    @error('password_confirmation')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__actions">
                    <button type="submit" class="button button--primary">登録する</button>
                </div>

                <p class="auth__link"> <a href="{{ route('login') }}">ログインはこちら</a></p>
            </form>
        </div>
    </main>
</body>

</html>