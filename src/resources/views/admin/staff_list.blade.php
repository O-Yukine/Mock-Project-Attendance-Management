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
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><a href="/admin/attendance/staff/detail">詳細</a></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
