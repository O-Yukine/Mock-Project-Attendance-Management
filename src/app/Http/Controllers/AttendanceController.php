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
}
