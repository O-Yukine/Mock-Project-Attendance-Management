<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
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

    public function showList()
    {
        $attendances = Attendance::with('breaks')->get();

        $attendances->each(function ($attendance) {
            $totalBreak = $attendance->breaks
                ->filter(fn($b) => $b->break_start && $b->break_end)
                ->sum(fn($b) => $b->break_start->diffInMinutes($b->break_end));

            $hours = floor($totalBreak / 60);
            $minutes = $totalBreak % 60;

            $attendance->total_break = sprintf('%02d:%02d', $hours, $minutes);
        });

        return view('attendance_list', [
            'attendances' => $attendances,
            'month' => now()->format('Y/m')
        ]);
    }

    public function showDetail()
    {
        return view('attendance_detail');
    }

    public function showRequest()
    {
        return view('request_list');
    }
}
