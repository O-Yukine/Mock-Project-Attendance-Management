@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <div class="list__title">
            <h1>{{ $day }} の勤怠</h1>
        </div>
        <div class="list__day"></div>
        <a href="">←前日</a>
        <a href="">{{ $day }}</a>
        <a href="">翌日→</a>
        <div class="list__content">
            <table>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ optional($attendance->clock_in)->format('H:i') ?? '' }}</td>
                        <td>{{ optional($attendance->clock_out)->format('H:i') ?? '' }}</td>
                        {{-- <td>{{ $attendance->breaks->break_start }}</td>
                        <td>{{ $attendance->breaks->break_end }}</td> --}}
                        <td><a href="/admin/attendance/detail/{{ $attendance->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
