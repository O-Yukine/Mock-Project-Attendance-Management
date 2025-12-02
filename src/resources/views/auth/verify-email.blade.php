<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/"><img src="{{ asset('images/COACHTECH.png') }}"
                        alt="coachtech logo"></a>
            </div>
        </div>
    </header>
    <main>
        <div class="email-verification">
            <div class="contents">
                <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
                <p>メール認証を完了してください</p>
                <div class="verify-link">
                    <a href="{{ $verificationUrl }}">認証はこちらから</a>
                </div>
                <form class="form" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="verification-resend__button">認証メールを再送する</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
