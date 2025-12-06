@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection
@section('content')
    <div class="request-list">
        <div class="request__title">
            <h1>申請一覧</h1>
        </div>
        <div class="request__content">
            <div class="request__upper">
                <a href="">承認待ち</a>
                <a href="">承認済み</a>
            </div>
            <div class="request__request__lower">
                <table>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><a href="/attendance/detail">詳細</a></td>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
