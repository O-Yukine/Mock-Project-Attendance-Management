<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\BreakTime;


class AttendanceController extends Controller
{
    public function index()

    {
        $attendance =  Attendance::where('user_id', auth()->id())->whereDate('created_at', today())->first();

        $status = $attendance->status ?? 'off';
        $statusLabel = [
            'off' => '勤務外',
            'working' => '出勤中',
            'on_break' => '休憩中',
            'clock_out' => '退勤済'
        ];

        $statusText = $statusLabel[$status];

        Carbon::setLocale('ja');

        return view('attendance', [
            'status' => $statusText,
            'today' => now()->isoformat('Y年M月D日(dd)'),
            'time' => now()->format('H:i'),
        ]);
    }

    public function updateAttendance(Request $request)
    {
        $attendance = Attendance::firstOrCreate([
            'user_id' => auth()->id(),
            'work_date' => $request->work_date,
        ]);

        switch ($request->action) {
            case 'start_working':
                $attendance->update([
                    'clock_in' => $request->time,
                    'status' => 'working'
                ]);
                break;

            case 'finish_working':
                $attendance->update([
                    'clock_out' => $request->time,
                    'status' => 'clock_out'
                ]);
                break;

            case 'break_start':
                $attendance->breaks()->create(['break_start' => $request->time]);
                $attendance->update(['status' => 'on_break']);
                break;

            case 'break_end':
                $break = $attendance->breaks()->whereNull('break_end')->orderBy('id', 'desc')->first();

                if ($break) {
                    $break->update(['break_end' => $request->time]);
                }

                $attendance->update(['status' => 'working']);
                break;
        }

        return redirect('/attendance');
    }

    public function showList(Request $request)
    {
        $month = now();
        $monthParam = $request->query('month');

        if ($monthParam) {
            $dateShow = Carbon::createFromFormat('Y/m', $monthParam);
        } else {
            $dateShow = $month;
        }

        $showYear = $dateShow->format('Y');
        $showMonth = $dateShow->format('m');

        $attendances = Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->whereYear('work_date', $showYear)
            ->whereMonth('work_date', $showMonth)
            ->get();

        $attendances->each(function ($attendance) {
            $totalBreak = $attendance->breaks
                ->filter(fn($b) => $b->break_start && $b->break_end)
                ->sum(fn($b) => $b->break_start->diffInMinutes($b->break_end));

            $hours = floor($totalBreak / 60);
            $minutes = $totalBreak % 60;

            $attendance->total_break = sprintf('%02d:%02d', $hours, $minutes);
            $attendance->total_break = ($attendance->total_break === '00:00') ?  '' : $attendance->total_break;
        });

        $lastMonth = $dateShow->clone()->subMonth()->format('Y/m');
        $nextMonth = $dateShow->clone()->addMonth()->format('Y/m');

        return view('attendance_list', [
            'attendances' => $attendances,
            'dateShow' => $dateShow->format('Y/m'),
            'lastMonth' => $lastMonth,
            'nextMonth' => $nextMonth,
        ]);
    }

    public function showDetail($id)
    {
        $attendanceLog = AttendanceLog::with('breaks')->where('attendance_id', $id)->first();
        $attendance = $attendanceLog ?? Attendance::with('breaks')->findOrFail($id);

        $userName = auth()->user()->name;

        return view('attendance_detail', compact('attendance', 'userName', 'id'));
    }

    public function updateDetail(Request $request, $id)
    {

        $detail = AttendanceLog::updateOrCreate([
            'user_id' => auth()->id(),
            'attendance_id' => $id,
            'work_date' => $request->work_date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        $breaks = $request->breaks;
        foreach ($breaks as $break) {

            $status = !empty($break['id']) ? 'update' : 'create';

            $detail->breaks()->updateOrCreate([
                'attendance_log_id' => $id,
                'break_time_id' => $break['id'] ?? null,
            ], [
                'break_start' => $break['break_start'],
                'break_end' => $break['break_end'],
                'action' => $status,
            ]);
        }

        return redirect("/attendance/detail/$id");
    }

    public function showRequest()
    {
        return view('request_list');
    }
}
