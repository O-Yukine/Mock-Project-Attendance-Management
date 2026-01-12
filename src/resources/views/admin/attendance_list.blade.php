@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <div class="list__title">
            <h1>{{ $dateShow->format('Y年m月d日') }} の勤怠</h1>
        </div>
        <div class="list__day">
            <a class="{{ request('day') == $yesterday ? 'active' : '' }}"
                href="/admin/attendance/list?day={{ $yesterday }}">←前日</a>
            <a class="{{ 'active' }}" href="#"><img src={{ asset('images/calendar.png') }}
                    alt="カレンダー">{{ $dateShow->format('Y/m/d') }}</a>
            <a class="{{ request('day') == $tomorrow ? 'active' : '' }}"
                href="/admin/attendance/list?day={{ $tomorrow }}">翌日→</a>
        </div>
        <div class="list__content">
            <table class="list__table">
                <tr class="list__table--row-title">
                    <th class="list__table--title">名前</th>
                    <th class="list__table--title">出勤</th>
                    <th class="list__table--title">退勤</th>
                    <th class="list__table--title">休憩</th>
                    <th class="list__table--title">合計</th>
                    <th class="list__table--title">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr class="list__table--row">
                        <td class="list__table--date">{{ $attendance->user->name }}</td>
                        <td class="list__table--date">{{ $attendance->clock_in?->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ $attendance->clock_out?->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ $attendance->total_break ?? '' }}</td>
                        <td class="list__table--date">
                            {{ $attendance->total_work_time ?? '' }}</td>
                        <td class="list__table--date"><a href="/admin/attendance/{{ $attendance->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
