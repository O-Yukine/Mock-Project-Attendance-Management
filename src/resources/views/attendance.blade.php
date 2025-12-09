@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="attendance">
        <div class="attendance__status">
            <p>{{ $status }}</p>
        </div>
        <form action="/attendance" method="post" class="form">
            @csrf
            <div class="attendance__date">
                <input type="hidden" name="work_date" value="{{ now()->toDateString() }}">
                {{ $today }}
            </div>
            <div class="attendance__time">
                <input type="hidden" name="clock_in" value="{{ now()->format('H:i') }}">
                {{ $time }}
            </div>
            <div class="attendance_submit">
                <button class="attendance__submit" type="submit">出勤</button>
            </div>
        </form>
    @endsection
