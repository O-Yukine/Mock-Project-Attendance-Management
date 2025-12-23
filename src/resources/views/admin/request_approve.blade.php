@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/request_approve.css') }}">
@endsection
@section('content')
    <div class="attendance-detail">
        <div class="detail__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="form" action="/admin/attendance/{{ $attendance->id }}" method="post">
            @csrf
            @method('patch')
            <table class="detail__table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td><input type="hidden" name="work_date" value="{{ $attendance->work_date->format('Y-m-d') }}">
                        {{ $attendance->work_date->format('Y年m月d日') }}
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td><input type="hidden" name="clock_in" value="{{ optional($attendance->clock_in)->format('H:i') }}">
                        {{ optional($attendance->clock_in)->format('H:i') }}〜{{ optional($attendance->clock_out)->format('H:i') }}
                        <input type="hidden" name="clock_out"
                            value="{{ optional($attendance->clock_out)->format('H:i') }}">
                    </td>
                </tr>
                @foreach ($attendance->breaks as $index => $break)
                    <tr>
                        <th>休憩{{ $index + 1 }}</th>
                        <td><input type="hidden" name="breaks[{{ $index }}][id]" value="{{ $break->id }}">
                            <input type="hidden" name="breaks[{{ $index }}][break_start]"
                                value="{{ optional($break->break_start)->format('H:i') }}">
                            {{ optional($break->break_start)->format('H:i') }}
                            〜{{ optional($break->break_end)->format('H:i') }}
                            <input type="hidden" name="breaks[{{ $index }}][break_end]"
                                value="{{ optional($break->break_end)->format('H:i') }}">
                        </td>
                    </tr>
                @endforeach
                @php
                    $newInput = $attendance->breaks->count();
                @endphp
                <tr>
                    <th>休憩{{ $newInput + 1 }}</th>
                    <td> <input type="hidden" name="breaks[{{ $newInput }}][break_start]">
                        <input type="hidden" name="breaks[{{ $newInput }}][break_end]">
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        {{ $attendance->reason }}
                    </td>
                </tr>
            </table>
            <button class="detail__submit" type="submit">承認</button>
        </form>
    </div>
@endsection
