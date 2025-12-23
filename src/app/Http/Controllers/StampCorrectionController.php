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
        } elseif (auth()->check()) {

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

            // if ($status === 'approved') {
            //     $attendances->push((object)[
            //         'id' => 9999,
            //         'attendnce_id' => 9999,
            //         'status' => '承認済み',
            //         'work_date' => now(),
            //         'reason' => 'ダミー申請理由',
            //         'created_at' => now(),
            //         'name' => auth()->user()->name,
            //     ]);
            // }
            return view('stamp_correction', compact('tab', 'attendances'));
        }
    }
}
