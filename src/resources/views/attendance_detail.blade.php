@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
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
                    <td>{{ $userName }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ $attendance->work_date->format('Y年') }}</td>
                    <td>{{ $attendance->work_date->format('m月d日') }}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td><input type="text" name="clock_in" value="{{ $attendance->clock_in->format('H:i') }}">〜<input
                            type="text" name="clock_out" value="{{ $attendance->clock_out->format('H:i') }}"></td>
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
                        <textarea name="reason">{{ $attendance->reason }}</textarea>
                    </td>
                </tr>
            </table>
            <button class="detail__submit" type="submit">修正</button>
        </form>
    </div>
@endsection
