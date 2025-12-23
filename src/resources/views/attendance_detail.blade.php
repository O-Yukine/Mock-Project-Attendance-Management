@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection
@section('content')
    <div class="attendance-detail">
        <div class="detail__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="form" action="/attendance/detail/{{ $id }}" method="post">
            @csrf
            <fieldset class="{{ $hasPendingRequest ? 'is-disabled' : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
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
                        <td><input type="text" name="clock_in"
                                value="{{ old('clock_in', optional($attendance->clock_in)->format('H:i')) }}">
                            〜
                            <input type="text" name="clock_out"
                                value="{{ old('clock_out', optional($attendance->clock_out)->format('H:i')) }}">
                            @error('clock_in')
                                <div class="form__error">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                    @foreach ($attendance->breaks as $index => $break)
                        <tr>
                            <th>休憩{{ $index + 1 }}</th>
                            <td><input type="hidden" name="breaks[{{ $index }}][id]"
                                    value="{{ $break->id ?? '' }}">
                                <input type="text" name="breaks[{{ $index }}][break_start]"
                                    value="{{ old("breaks.$index.break_start", optional($break->break_start)->format('H:i')) }}">〜<input
                                    type="text" name="breaks[{{ $index }}][break_end]"
                                    value="{{ old("breaks.$index.break_end", optional($break->break_end)->format('H:i')) }}">
                                @error("breaks.$index.break_start")
                                    <div class="form__error">{{ $message }}</div>
                                @enderror
                                @error("breaks.$index.break_end")
                                    <div class="form__error">{{ $message }}</div>
                                @enderror

                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>備考</th>
                        <td>
                            <textarea name="reason">{{ old('reason', $attendance->reason) }}</textarea>
                            @error('reason')
                                <div class="form__error">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                </table>
            </fieldset>
            @if ($hasPendingRequest)
                <p class="detail__notice">
                    *承認待ちのため修正はできません。
                </p>
            @else
                <button class="detail__submit" type="submit">修正</button>
            @endif
        </form>
    </div>
@endsection
