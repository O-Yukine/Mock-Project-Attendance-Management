@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/staff_list.css') }}">
@endsection
@section('content')
    <div class="staff-list">
        <div class="list__title">
            <h1>スタッフ一覧</h1>
        </div>
        <div class="list__table">
            <table>
                <tr class="list__table-row">
                    <th class="list__table-title">名前</th>
                    <th class="list__table-title">メールアドレス</th>
                    <th class="list__table-title">月次勤怠</th>
                </tr>
                @foreach ($staffs as $staff)
                    <tr class="list__table-row">
                        <td class="list__table-date">{{ $staff->name }}</td>
                        <td class="list__table-date">{{ $staff->email }}</td>
                        <td class="list__table-date"><a href="/admin/attendance/staff/{{ $staff->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
