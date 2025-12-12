<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = 1;

        for ($day = 1; $day <= 30; $day++) {

            $date = Carbon::create(2025, 11, $day)->toDateString();
            $worked = rand(0, 9) < 8 ? 1 : 0;
            $clockIn = $worked ? rand(8, 10) . ':' . rand(0, 59) : null;
            $clockOut = $worked ? rand(17, 19) . ':' . rand(0, 59) : null;

            $attendance = Attendance::create([
                'user_id' => $userId,
                'work_date' => $date,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'status' => $worked ? 'clock_out' : 'off'
            ]);

            if ($worked) {
                $attendance->breaks()->create([
                    'attendance_id' => $attendance->id,
                    'break_start' => rand(12, 13) . ':00',
                    'break_end' => rand(12, 13) . ':30',
                ]);
            }
        }
    }
}
