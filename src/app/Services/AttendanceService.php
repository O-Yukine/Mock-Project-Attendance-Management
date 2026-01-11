<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * ユーザーの出勤・退勤・休憩管理
     */

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
    /**
     * ユーザーによる勤怠修正リクエスト(LOGの作成)
     */
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
    /**
     * 管理者による直接の勤怠修正
     */
    public function adminUpdateAttendance(Attendance $attendance, array $data, array $breaks): AttendanceLog
    {
        return DB::transaction(function () use ($attendance, $data, $breaks) {

            $workDate = Carbon::parse($data['work_date'])->format('Y-m-d');

            $attendanceLog = $attendance->attendanceLogs()->create([
                'user_id'      => $attendance->user->id,
                'work_date'    => $workDate,
                'clock_in'     => $data['clock_in'],
                'clock_out'    => $data['clock_out'],
                'reason'       => $data['reason'],
                'status'       => 'approved',
                'requested_by' => 'admin',
            ]);


            foreach ($breaks as $break) {
                if (empty($break['break_start']) || empty($break['break_end'])) continue;

                $attendanceLog->breaks()->create([
                    'break_time_id' => $break['id'] ?? null,
                    'break_start'  => $break['break_start'],
                    'break_end'    => $break['break_end'],
                ]);
            }


            $attendance->update([
                'work_date' => $workDate,
                'clock_in'  => $data['clock_in'],
                'clock_out' => $data['clock_out'],
            ]);


            foreach ($breaks as $break) {
                if (empty($break['break_start']) || empty($break['break_end'])) continue;

                $attendance->breaks()->updateOrCreate(
                    ['id' => $break['id'] ?? null],
                    [
                        'break_start' => $break['break_start'],
                        'break_end'   => $break['break_end'],
                    ]
                );
            }

            return $attendanceLog;
        });
    }
}
