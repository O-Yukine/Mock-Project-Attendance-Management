@guest
    <nav class="nav">
        <ul class="header-nav">
            <li class="header-nav__item">
                <a class="header-nav__link" href="/login">ログイン</a>
            </li>

            <li class="header-nav__item">
                <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__sell" href="/sell">出品</a>
            </li>
        </ul>
    </nav>
@else
    <nav class="nav">
        <ul class="header-nav">
            <li class="header-nav__item">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit"class="header-nav__button">ログアウト</button>
                </form>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__sell" href="/sell">出品</a>
            </li>
        </ul>
    </nav>
@endguest
