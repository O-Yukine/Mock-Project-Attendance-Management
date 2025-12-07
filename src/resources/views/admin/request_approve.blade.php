@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/request_approve.css') }}">
@endsection
@section('content')
    <div class="request">
        <div class="request__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="request__table" action="" class="form">
            <table>
                <tr>
                    <th>名前</th>
                    <td></td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td></td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td></td>

                </tr>
                <tr>
                    <th>休憩</th>
                    <td></td>

                </tr>
                <tr>
                    <th>休憩２</th>
                    <td></td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td></td>
                </tr>
            </table>
            <button class="request__submit" type="submit">承認</button>
        </form>
    </div>
@endsection
