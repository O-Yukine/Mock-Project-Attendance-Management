<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    @livewireStyles
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/"><img src="{{ asset('images/COACHTECH.png') }}"
                        alt="coachtech logo"></a>
                @unless (Request::is('register') || Request::is('login') || Request::is('/email/verify'))
                    <div class="search-bar">
                        <form class="form" action="/" method="get">
                            <input type="text" name="keyword" value="{{ request('keyword', session('keyword', '')) }}"
                                placeholder="なにをお探しですか？">
                        </form>
                    </div>
                    @include('layouts.header_nav')
                @endunless
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    @livewireScripts
</body>

</html>
