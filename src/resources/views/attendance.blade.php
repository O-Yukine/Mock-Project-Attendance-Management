@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="attendance">
        <div class="attendance__status">
            <p>{{ $statusLabel }}</p>
        </div>
        <form action="/attendance" method="post" class="form">
            @csrf
            <div class="attendance__date">
                <input type="hidden" name="work_date" value="{{ now()->toDateString() }}">
                {{ $today }}
            </div>
            <div class="attendance__time">
                <input type="hidden" name="time" value="{{ now()->format('H:i') }}">
                {{ $time }}
            </div>
            <div class="attendance__submit">
                @if ($statusCode == 'off')
                    <button class="attendance__submit--button" name="action" value="start_working"
                        type="submit">出勤</button>
                @elseif($statusCode == 'working')
                    <button class="attendance__submit--button" name="action" value="finish_working"
                        type="submit">退勤</button>
                    <button class="break__submit--button" name="action" value="break_start" type="submit">休憩入</button>
                @elseif($statusCode == 'on_break')
                    <button class="break__submit--button" name="action" value="break_end" type="submit">休憩戻</button>
                @elseif($statusCode == 'clock_out')
                    <p class="status__notice">お疲れ様でした。</p>
                @endif
            </div>
        </form>
    @endsection
