<nav class="nav">
    <ul class="header-nav">
        <li class="header-nav__item">
            <a class="header-nav__link" href="">スタッフ一覧</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__sell" href="">申請一覧</a>
        </li>
        <li class="header-nav__item">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="header-nav__button">
                    ログアウト
                </button>
            </form>
        </li>
    </ul>
</nav>
