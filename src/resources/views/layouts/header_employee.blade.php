<nav class="nav">
    <ul class="header-nav">
        <li class="header-nav__item">
            <a class="header-nav__link" href="/attendance">勤怠</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__link" href="/attendance/list">勤怠一覧</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__sell" href="/stamp_correction_request/list">申請</a>
        </li>
        <li class="header-nav__item">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="header-nav__button">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
