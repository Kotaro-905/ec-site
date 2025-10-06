<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'COACHTECH')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    @yield('css')
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

    <!-- メイン -->
    <main class="page">
        @yield('content')
    </main>
</body>
</html>