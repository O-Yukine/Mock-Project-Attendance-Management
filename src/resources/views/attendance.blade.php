@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="attendance">
        <div class="attendance__status">
            <p>勤務外</p>
        </div>
        <div class="attendance__date">
            {{ $today }}
        </div>
        <div class="attendance__time">
            {{ $time }}
        </div>
        <div class="attendance_submit">
            <form action="" class="form">
                <button class="attendance__submit" type="submit">出勤</button>
            </form>
        </div>
    @endsection
