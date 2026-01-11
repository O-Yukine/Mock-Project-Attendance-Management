<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;

class AttendanceService
{

    public function updateAttendance(Attendance $attendance, string $action, string $time)
    {
        DB::transaction(function () use ($attendance, $action, $time) {


            switch ($action) {
                case 'start_working':

                    if ($attendance->status !== 'off') {
                        abort(400);
                    }

                    $attendance->update([
                        'clock_in' => $time,
                        'status' => 'working'
                    ]);
                    break;

                case 'finish_working':
                    if ($attendance->status !== 'working') {
                        abort(400);
                    }
                    $attendance->update([
                        'clock_out' => $time,
                        'status' => 'clock_out'
                    ]);
                    break;

                case 'break_start':

                    if ($attendance->status !== 'working') {
                        abort(400);
                    }
                    $attendance->breaks()->create(['break_start' => $time]);
                    $attendance->update(['status' => 'on_break']);
                    break;

                case 'break_end':
                    if ($attendance->status !== 'on_break') {
                        abort(400);
                    }
                    $break = $attendance->breaks()->whereNull('break_end')->orderBy('id', 'desc')->first();

                    if (!$break) abort(400);
                    $break->update(['break_end' => $time]);
                    $attendance->update(['status' => 'working']);
                    break;
            }
        });
    }

    public function requestDetailCorrection(int $userId, int $attendanceId, array $data, array $breaks): AttendanceLog
    {

        return DB::transaction(function () use ($userId, $attendanceId, $data, $breaks) {
            $detail = AttendanceLog::create([
                'user_id' => $userId,
                'attendance_id' => $attendanceId,
                'work_date' => $data['work_date'],
                'clock_in' => $data['clock_in'],
                'clock_out' => $data['clock_out'],
                'reason' => $data['reason'],
                'status' => 'pending',
                'requested_by' => 'user'
            ]);


            foreach ($breaks as $break) {

                if (
                    empty($break['break_start']) ||
                    empty($break['break_end'])
                ) {
                    continue;
                }

                $detail->breaks()->create([
                    'break_time_id' => $break['id'] ?? null,
                    'break_start' => $break['break_start'],
                    'break_end' => $break['break_end'],
                ]);
            }

            return $detail;
        });
    }
}
