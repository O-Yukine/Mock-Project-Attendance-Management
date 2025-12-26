@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <div class="list__title">
            <h1>勤怠一覧</h1>
        </div>
        <div class="list__day">
            <a class="{{ request('month') == $lastMonth ? 'active' : '' }}"
                href="/attendance/list?month={{ $lastMonth }}">←前月</a>
            <a class="active" href="">{{ $dateShow }}</a>
            <a class="{{ request('month') == $nextMonth ? 'active' : '' }}"
                href="/attendance/list?month={{ $nextMonth }}">翌月→</a>
        </div>
        <div class="list__content">
            <table class="list__table">
                <tr class="list__table--row-title">
                    <th class="list__table--title">日付</th>
                    <th class="list__table--title">出勤</th>
                    <th class="list__table--title">退勤</th>
                    <th class="list__table--title">休憩</th>
                    <th class="list__table--title">合計</th>
                    <th class="list__table--title">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr class="list__table--row">
                        <td class="list__table--date">{{ $attendance->work_date->isoFormat('MM/DD(ddd)') }}</td>
                        <td class="list__table--date">{{ $attendance->clock_in?->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ $attendance->clock_out?->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ $attendance->total_break ?? '' }}</td>
                        <td class="list__table--date">
                            {{ $attendance->clock_in?->diff($attendance->clock_out)?->format('%H:%I') ?? '' }}</td>
                        <td class="list__table--date"><a href="/attendance/detail/{{ $attendance->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
