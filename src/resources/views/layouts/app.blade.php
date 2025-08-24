<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'COACHTECH')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header__bar">
            <div class="header__inner">
                <a href="{{ url('/') }}" class="header__logo">
                    <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="header__logo-img">
                </a>

                <form class="header__search" action="{{ url('/search') }}" method="get" role="search">
                    <input class="header__search-input" type="search" name="q" placeholder="なにをお探しですか？" aria-label="検索">
                </form>

                <nav class="header__nav">
                    @auth
                    <!-- ログアウト（実際に機能する） -->
                    <a class="header__link" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        ログアウト
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="post" style="display:none;">
                        @csrf
                    </form>

                    <!-- ダミーリンク（あとで修正予定） -->
                    <a class="header__link" href="#">マイページ</a>
                    <a class="header__button" href="#">出品</a>
                    @else
                    <a class="header__link" href="#">ログイン</a>
                    <a class="header__link" href="#">会員登録</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="page">
        @yield('content')
    </main>
</body>

</html>