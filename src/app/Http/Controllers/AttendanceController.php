<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance', [
            'today' => now()->format('Y年m月d日'),
            'time' => now()->format('H:i'),
        ]);
    }

    public function showList()
    {
        return view('attendance_list', [
            'month' => now()->format('Y/m')
        ]);
    }

    public function showDetail()
    {
        return view('attendance_detail');
    }
}
