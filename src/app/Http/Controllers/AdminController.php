<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAttendanceDetailRequest;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\User;
use Carbon\Carbon;
use App\Services\AttendanceService;


class AdminController extends Controller
{

    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }


    public function showAttendanceList(Request $request)
    {
        $dayParam = $request->query('day');

        $date = $dayParam ? Carbon::createFromFormat('Y/m/d', $dayParam) : today();

        $attendances = Attendance::with(['breaks', 'user'])
            ->where('work_date', $date->toDateString())
            ->orderByRaw('clock_in IS NULL')
            ->orderBy('clock_in', 'asc')
            ->get();

        $dateShow  = $date->clone();
        $yesterday = $date->clone()->subDay()->format('Y/m/d');
        $tomorrow = $date->clone()->addDay()->format('Y/m/d');

        return view('admin/attendance_list', compact('attendances', 'yesterday', 'dateShow', 'tomorrow'));
    }

    public function showDetail($id)
    //この$idはattendanceId
    {
        $attendanceLog = AttendanceLog::with('breaks')
            ->where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();

        $hasPendingRequest = $attendanceLog !== null;

        $attendance = $attendanceLog ?? Attendance::with('breaks')->findOrFail($id);

        return view('admin/attendance_detail', compact('attendance', 'id', 'hasPendingRequest'));
    }

    public function updateDetail(AdminAttendanceDetailRequest $request, $id)
    {

        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);

        $this->attendanceService->adminUpdateAttendance(
            $attendance,
            $request->only(['work_date', 'clock_in', 'clock_out', 'reason']),
            $request->input('breaks', [])
        );

        return redirect('/admin/attendance/' . $id);
    }

    public function showStaffList()
    {
        $staffs = User::select('id', 'name', 'email')->get();

        return view('admin/staff_list', compact('staffs'));
    }

    public function showStaffAttendanceList(Request $request, $id)
    {

        $month = \Carbon\Carbon::createFromFormat(
            'Y/m',
            $request->query('month', now()->format('Y/m'))
        );

        $staff = User::findOrFail($id);

        $attendances = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->get();

        $last_month = $month->clone()->subMonth()->format('Y/m');
        $next_month = $month->clone()->addMonth()->format('Y/m');
        $showMonth = $month->format('Y/m');

        return view('admin/staff_attendance_list', compact('last_month', 'next_month', 'showMonth', 'staff', 'attendances'));
    }
}
