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
        <div class="list__day"></div>
        <a href="">←前月</a>
        <a href="">{{ $month }}</a>
        <a href="">翌月→</a>
        <div class="list__content">
            <table>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->work_date->isoFormat('MM/DD(ddd)') }}</td>
                        <td>{{ $attendance->clock_in->format('H:i') }}</td>
                        <td>{{ $attendance->clock_out->format('H:i') }}</td>
                        <td>{{ $attendance->total_break }}</td>
                        <td>{{ $attendance->clock_in->diff($attendance->clock_out)->format('%H:%I') }}</td>
                        <td><a href="/attendance/detail/{{ $attendance->id }}">詳細</a></td> --}}
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
