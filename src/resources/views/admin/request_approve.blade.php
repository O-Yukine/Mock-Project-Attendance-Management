@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/request_approve.css') }}">
@endsection
@section('content')
    <div class="request-approve">
        <div class="approve__title">
            <h1>勤怠詳細</h1>
        </div>
        <form class="form" action="/stamp_correction_request/approve/{{ $attendance->id }}" method="post">
            @csrf
            @method('patch')
            <table class="approve__table">
                <tr class="approve__table--row">
                    <th class="approve__table--title">名前</th>
                    <td class="approve__table--date">{{ $attendance->user->name }}</td>
                </tr>
                <tr class="approve__table--row">
                    <th class="approve__table--title">日付</th>
                    <td class="detail__table--date">
                        <input type="hidden" name="work_date" value="{{ $attendance->work_date->format('Y-m-d') }}">
                        <span class="date__year"> {{ $attendance->work_date->format('Y年') }} </span>
                        <span class="date__md"> {{ $attendance->work_date->format('m月d日') }} </span>
                    </td>
                </tr>
                <tr class="approve__table--row">
                    <th class="approve__table--title">出勤・退勤</th>
                    <td class="approve__table--date">
                        <input type="hidden" name="clock_in" value="{{ $attendance->clock_in?->format('H:i') ?? '' }}">
                        {{ $attendance->clock_in?->format('H:i') ?? '' }}
                        〜
                        {{ $attendance->clock_out?->format('H:i') ?? '' }}
                        <input type="hidden" name="clock_out" value="{{ $attendance->clock_out?->format('H:i') ?? '' }}">
                    </td>
                </tr>
                @php
                    $breaks = $attendance->breaks->concat([['id' => null, 'break_start' => null, 'break_end' => null]]);
                @endphp
                @foreach ($attendance->breaks as $index => $break)
                    <tr class="approve__table--row">
                        <th class="approve__table--title">
                            {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                        </th>
                        <td class="approve__table--date">
                            <input type="hidden" name="breaks[{{ $index }}][break_time_id]"
                                value="{{ $break->break_time_id ?? '' }}">
                            <input type="hidden" name="breaks[{{ $index }}][break_start]"
                                value="{{ $break->break_start?->format('H:i') ?? '' }}">
                            {{ $break->break_start?->format('H:i') ?? '' }}
                            〜
                            {{ $break->break_end?->format('H:i') ?? '' }}
                            <input type="hidden" name="breaks[{{ $index }}][break_end]"
                                value="{{ $break->break_end?->format('H:i') ?? '' }}">
                        </td>
                    </tr>
                @endforeach
                <tr class="approve__table--row">
                    <th class="approve__table--title">備考</th>
                    <td class="approve__table--date">
                        {{ $attendance->reason }}
                    </td>
                </tr>
            </table>
            <div class="approve__submit">
                @if ($attendance->status === 'pending')
                    <button class="approve__submit--button" type="submit">承認</button>
                @else
                    <p class="approve__notice">承認済み</p>
                @endif
            </div>
        </form>
    </div>
@endsection
