<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAttendanceDetailRequest;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
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
        // dd($attendance->clock_in, $attendance->clock_out);


        return view('admin/attendance_detail', compact('attendance', 'id', 'hasPendingRequest'));
    }

    public function updateDetail(AdminAttendanceDetailRequest $request, $id)
    {

        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);

        DB::transaction(function () use ($request, $attendance) {

            $attendanceLog = $attendance->attendanceLogs()->create(
                [
                    'user_id' => $attendance->user->id,
                    'work_date' => Carbon::parse($request->work_date)->format('Y-m-d'),
                    'clock_in' => $request->clock_in,
                    'clock_out' => $request->clock_out,
                    'reason' => $request->reason,
                    'status' => 'approved',
                    'requested_by' => 'admin'
                ]
            );

            $attendance->update([
                'work_date' => $request->work_date,
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
            ]);

            foreach ($request->breaks as $break) {

                if (empty($break['break_start']) || empty($break['break_end'])) {
                    continue;
                }

                $attendanceLog->breaks()->create([
                    'break_time_id' => $break['id'] ?? null,
                    'break_start' => $break['break_start'],
                    'break_end'   => $break['break_end'],
                ]);
            }


            foreach ($request->breaks as $break) {

                if (empty($break['break_start']) || empty($break['break_end'])) {
                    continue;
                }


                if (!empty($break['id'])) {
                    $attendance->breaks()->where('id', $break['id'])->update([
                        'break_start' => $break['break_start'],
                        'break_end'   => $break['break_end'],

                    ]);
                } else {

                    $attendance->breaks()->create([
                        'break_start' => $break['break_start'],
                        'break_end'   => $break['break_end'],
                    ]);
                }
            }
        });

        return redirect(
            '/admin/attendance/list?day=' .
                Carbon::parse($attendance->work_date)->format('Y/m/d')
        );
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
