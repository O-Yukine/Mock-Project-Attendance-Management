<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showAttendanceList(Request $request)
    {
        $dayParam = $request->query('day');

        $date = $dayParam ? Carbon::createFromFormat('Y/m/d', $dayParam) : today()->subMonth();

        $attendances = Attendance::with(['breaks', 'user'])
            ->where('work_date', $date->toDateString())
            ->get();

        $attendances->each(function ($attendance) {
            $totalBreak = $attendance->breaks
                ->filter(fn($b) => $b->break_start && $b->break_end)
                ->sum(fn($b) => $b->break_start->diffInMinutes($b->break_end));

            $hours = floor($totalBreak / 60);
            $minutes = $totalBreak % 60;

            $attendance->total_break = sprintf('%02d:%02d', $hours, $minutes);
            $attendance->total_break = ($attendance->total_break === '00:00') ? '' : $attendance->total_break;
        });

        $dateShow  = $date->format('Y/m/d');
        $yesterday = $date->clone()->subDay()->format('Y/m/d');
        $tomorrow = $date->clone()->addDay()->format('Y/m/d');


        return view('admin/attendance_list', compact('attendances', 'yesterday', 'dateShow', 'tomorrow'));
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
