<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;


class StampCorrectionController extends Controller
{
    public function index(Request $request)
    {
        if (auth('admin')->check()) {

            $tab = $request->query('tab', 'pending');

            $status = in_array($tab, ['pending', 'approved']) ? $tab : 'pending';

            $attendances = AttendanceLog::with('user')
                ->where('status', $status)
                ->where('requested_by', 'user')
                ->select(['id', 'user_id', 'attendance_id', 'status', 'work_date', 'created_at', 'reason'])
                ->get();

            $attendances->each(function ($attendance) {

                $attendance->status = $attendance->status === 'pending' ? '承認待ち' : '承認済み';
                $attendance->name = $attendance->user->name;
            });

            return view('admin/stamp_correction', compact('tab', 'attendances'));
        } elseif (auth('web')->check()) {

            $tab = $request->query('tab', 'pending');

            $status = in_array($tab, ['pending', 'approved']) ? $tab : 'pending';


            $attendances = AttendanceLog::where('user_id', auth()->id())
                ->where('status', $status)
                ->where('requested_by', 'user')
                ->select(['id', 'attendance_id', 'status', 'work_date', 'created_at', 'reason'])
                ->get();

            $attendances->each(function ($attendance) {

                $attendance->status = $attendance->status === 'pending' ? '承認待ち' : '承認済み';
                $attendance->name = auth()->user()->name;
            });

            return view('stamp_correction', compact('tab', 'attendances'));
        }
    }

    public function requestShow($attendance_correct_request_id)
    {
        $attendance = AttendanceLog::with('breaks', 'user')
            ->where('id', $attendance_correct_request_id)
            ->firstOrFail();

        return view('admin/request_approve', compact('attendance'));
    }

    public function requestApprove(Request $request, $attendance_correct_request_id)
    {
        $attendanceLog = AttendanceLog::with('attendance.breaks')
            ->where('id', $attendance_correct_request_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $attendance = $attendanceLog->attendance;

        $attendance->update([
            'clock_in'  => $request->clock_in,
            'clock_out' => $request->clock_out,
        ]);

        foreach ($request->breaks as $break) {
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

        $attendanceLog->update(['status' => 'approved']);

        return redirect("/stamp_correction_request/approve/{$attendance_correct_request_id}");
    }
}
