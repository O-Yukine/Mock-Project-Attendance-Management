@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection
@section('content')
    <div class="attendacne-detail">
        <div class="detail__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="detail__table" action="" class="form">
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
                    <td><input type="text">〜<input type="text"></td>

                </tr>
                <tr>
                    <th>休憩</th>
                    <td><input type="text">〜<input type="text"></td>

                </tr>
                <tr>
                    <th>休憩２</th>
                    <td><input type="text">〜<input type="text"></td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="" id=""></textarea>
                    </td>
                </tr>
            </table>
            <button class="detail__submit" type="submit">修正</button>
        </form>
    </div>
@endsection
