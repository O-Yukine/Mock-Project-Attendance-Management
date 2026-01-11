<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceDetailRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;




class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }


    public function index()

    {
        $attendance =  Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        $statusCode = $attendance->status ?? 'off';
        $statusLabel = [
            'off' => '勤務外',
            'working' => '出勤中',
            'on_break' => '休憩中',
            'clock_out' => '退勤済'
        ][$statusCode];

        Carbon::setLocale('ja');

        return view('attendance', [
            'statusCode' => $statusCode,
            'statusLabel' => $statusLabel,
            'today' => now()->isoformat('Y年M月D日(dd)'),
            'time' => now()->format('H:i'),
        ]);
    }

    public function updateAttendance(Request $request)
    {
        $attendance = Attendance::firstOrCreate([
            'user_id' => auth()->id(),
            'work_date' => $request->work_date,
        ], [
            'status' => 'off',
        ]);

        $this->attendanceService->updateAttendance($attendance, $request->action, $request->time);

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
    // この$id は attendanceId
    {
        $attendanceLog = AttendanceLog::with('breaks')
            ->where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();

        //logがあればlog ,なければattendance (attendance_id = $attendance->attendance_id / $id)
        $attendance = $attendanceLog ?? Attendance::with('breaks', 'user')->findOrFail($id);

        $hasPendingRequest = $attendanceLog !== null;

        return view('attendance_detail', compact('attendance', 'id', 'hasPendingRequest'));
    }

    public function updateDetail(AttendanceDetailRequest $request, $id)
    { // この$id は attendanceId

        $this->attendanceService->requestDetailCorrection(
            auth()->id(),
            $id,
            $request->only(['work_date', 'clock_in', 'clock_out', 'reason']),
            $request->input('breaks', [])
        );

        return redirect("/attendance/detail/$id");
    }
}
