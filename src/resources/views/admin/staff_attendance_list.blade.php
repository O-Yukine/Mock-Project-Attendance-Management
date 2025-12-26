@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/staff_attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <div class="list__title">
            <h1>{{ $staff->name }}さんの勤怠</h1>
        </div>
        <div class="list__day">
            <a class="{{ request('month') == $last_month ? 'active' : '' }}"
                href="/admin/attendance/staff/{{ $staff->id }}?month={{ $last_month }}">←前月</a>
            <a class="{{ 'active' }}" href="">{{ $month->format('Y/m') }}</a>
            <a class="{{ request('month') == $next_month ? 'active' : '' }}"
                href="/admin/attendance/staff/{{ $staff->id }}?month={{ $next_month }}">翌月→</a>
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
                        <td class="list__table--date">{{ $attendance->work_date->format('m/d') }}</td>
                        <td class="list__table--date">{{ optional($attendance->clock_in)->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ optional($attendance->clock_out)->format('H:i') ?? '' }}</td>
                        <td class="list__table--date">{{ $attendance->total_break ?? '' }}</td>
                        <td class="list__table--date">
                            {{ $attendance->clock_in?->diff($attendance->clock_out)?->format('%H:%I') ?? '' }}</td>
                        <td class="list__table--date"><a href="/admin/attendance/{{ $attendance->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="csv">
            <button class="csv__button" type="submit">CSV出力</button>
        </div>
    </div>
@endsection
