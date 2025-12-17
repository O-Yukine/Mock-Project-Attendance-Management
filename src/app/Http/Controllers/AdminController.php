<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminController extends Controller
{
    public function showAttendanceList()
    {
        $day = now()->subMonth()->toDateString();

        $attendances = Attendance::with(['breaks', 'user'])
            ->where('work_date', $day)
            ->get();

        return view('admin/attendance_list', compact('attendances', 'day'));
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

    public function requestApprove()
    {
        return view('admin/request_approve');
    }
}
