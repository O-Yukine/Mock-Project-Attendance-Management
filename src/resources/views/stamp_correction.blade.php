@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stamp_correction.css') }}">
@endsection
@section('content')
    <div class="request-list">
        <div class="request__title">
            <h1>申請一覧</h1>
        </div>
        <div class="request__content">
            <div class="request__upper">
                <a class="tab{{ $tab === 'pending' ? 'active' : '' }}"
                    href="/stamp_correction_request/list?tab=pending">承認待ち</a>
                <a class="tab{{ $tab === 'approved' ? 'active' : '' }}"
                    href="/stamp_correction_request/list?tab=approved">承認済み</a>
            </div>
            <div class="request__request__lower">
                <table>
                    <tr>
                        <th class="request__table--title">状態</th>
                        <th class="request__table--title">名前</th>
                        <th class="request__table--title">対象日時</th>
                        <th class="request__table--title">申請理由</th>
                        <th class="request__table--title">申請日時</th>
                        <th class="request__table--title">詳細</th>
                    </tr>
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td class="request__table--date">{{ $attendance->status }}</td>
                            <td class="request__table--date">{{ $attendance->name }}</td>
                            <td class="request__table--date">{{ $attendance->work_date->format('Y/m/d') }}</td>
                            <td class="request__table--date">{{ $attendance->reason }}</td>
                            <td class="request__table--date">{{ $attendance->created_at }}</td>
                            <td class="request__table--date"><a href="/attendance/detail/{{ $attendance->id }}">詳細</a></td>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
