<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
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

    public function showDetail($id)
    {
        $attendance = Attendance::with(['breaks', 'user'])
            ->findOrFail($id);

        return view('admin/attendance_detail', compact('attendance'));
    }

    public function updateDetail(Request $request, $id)
    {

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in'  => $request->clock_in,
            'clock_out' => $request->clock_out,
        ]);

        foreach ($request->breaks as $break) {
            if (!empty($break['id'])) {

                $attendance->breaks()->where('id', $break['id'])->update([
                    'break_start' => $break['break_start'],
                    'break_end'   => $break['break_end'],
                    'reason'      => $break['reason'],
                ]);
            } else {

                $attendance->breaks()->create([
                    'break_start' => $break['break_start'],
                    'break_end'   => $break['break_end'],
                    'reason'      => $break['reason'],
                ]);
            }
        }

        return redirect("/admin/attendance/detail/{$id}");
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
