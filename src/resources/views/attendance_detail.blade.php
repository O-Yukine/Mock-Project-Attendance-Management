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
                    <td><input type="text" name="clock_in" value="{{ optional($attendance->clock_in)->format('H:i') }}">
                        〜
                        <input type="text" name="clock_out"
                            value="{{ optional($attendance->clock_out)->format('H:i') }}">
                    </td>
                </tr>
                @foreach ($attendance->breaks as $index => $break)
                    <tr>
                        <th>休憩{{ $index + 1 }}</th>
                        <td><input type="hidden" name="breaks[{{ $index }}][id]" value="{{ $break->id ?? '' }}">
                            <input type="text" name="breaks[{{ $index }}][break_start]"
                                value="{{ optional($break->break_start)->format('H:i') }}">〜<input type="text"
                                name="breaks[{{ $index }}][break_end]"
                                value="{{ optional($break->break_end)->format('H:i') }}">
                        </td>
                    </tr>
                @endforeach
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
