<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;


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

    public function startWorking(Request $request)
    {

        Attendance::create([
            'user_id' => auth()->id(),
            'work_date' => $request->work_date,
            'clock_in' => $request->clock_in,
            'status' => 'working',
        ]);

        return redirect('/attendance');
    }

    public function showList()
    {
        return view('attendance_list', [
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
