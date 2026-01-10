@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection
@section('content')
    <div class="attendance-detail">
        <div class="detail__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="form" action="/admin/attendance/{{ $id }}" method="post">
            @csrf
            @method('patch')
            <fieldset class="{{ $hasPendingRequest ? 'is-disabled' : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                <table class="detail__table">
                    <tr class="detail__table--row">
                        <th class="detail__table--title">名前</th>
                        <td class="detail__table--date name-field">{{ $attendance->user->name }}</td>
                    </tr>
                    <tr class="detail__table--row">
                        <th class="detail__table--title">日付</th>
                        <td class="detail__table--date">
                            <div class="input-day"><input type="hidden" name="work_date"
                                    value="{{ $attendance->work_date->format('Y-m-d') }}">
                                <span class="date__year">
                                    {{ $attendance->work_date->format('Y年') }}
                                </span>
                                <span class="date__md">
                                    {{ $attendance->work_date->format('m月d日') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr class="detail__table--row">
                        <th class="detail__table--title">出勤・退勤</th>
                        <td class="detail__table--date">
                            <div class="input-row"><input type="text" name="clock_in"
                                    value="{{ old($attendance->clock_in?->format('H:i') ?? '') }}">
                                〜
                                <input type="text" name="clock_out"
                                    value="{{ old(optional($attendance->clock_out)->format('H:i')) }}">
                            </div>
                            @error('clock_in')
                                <div class="form__error">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                    @php
                        $breaks = $attendance->breaks->concat([
                            ['id' => null, 'break_start' => null, 'break_end' => null],
                        ]);
                    @endphp
                    @foreach ($breaks as $index => $break)
                        <tr class="detail__table--row">
                            <th class="detail__table--title">
                                {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                            </th>
                            <td class="detail__table--date">
                                <div class="input-row">
                                    <input type="hidden" name="breaks[{{ $index }}][id]"
                                        value="{{ $break['id'] ?? '' }}">
                                    <input type="text" name="breaks[{{ $index }}][break_start]"
                                        value="{{ old("breaks.$index.break_start", optional($break['break_start'])->format('H:i')) }}">
                                    〜
                                    <input type="text" name="breaks[{{ $index }}][break_end]"
                                        value="{{ old("breaks.$index.break_end", optional($break['break_end'])->format('H:i')) }}">
                                </div>
                                @error("breaks.$index.break_start")
                                    <div class="form__error">{{ $message }}</div>
                                @enderror
                                @error("breaks.$index.break_end")
                                    <div class="form__error">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                    <tr class="detail__table--row">
                        <th class="detail__table--title">備考</th>
                        <td class="detail__table--date">
                            <textarea name="reason">{{ old('reason', $attendance->reason ?? '') }}</textarea>
                            @error('reason')
                                <div class="form__error">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                </table>
            </fieldset>
            <div class="detail__submit">
                @if ($hasPendingRequest)
                    <p class="detail__notice">
                        *承認待ちのため修正はできません。
                    </p>
                @else
                    <button class="detail__submit--button" type="submit">修正</button>
                @endif
            </div>
        </form>
    </div>
@endsection
