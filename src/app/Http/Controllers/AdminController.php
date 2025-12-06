<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showList()
    {
        return view('admin/attendance_list', ['day' => now()->format('Y年m月d日')]);
    }

    public function showDetail()
    {
        return view('admin/attendance_detail');
    }

    public function showStaff()
    {

        return view('admin/staff_list');
    }

    public function showStaffAttendanceList()
    {

        return view('admin/staff_attendance_list', [
            'month' => now()->format('Y/m')
        ]);
    }
}
