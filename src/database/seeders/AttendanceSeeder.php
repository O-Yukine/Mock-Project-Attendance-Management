<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            for ($day = 1; $day <= 30; $day++) {

                $date = Carbon::create(2025, 11, $day)->toDateString();
                $worked = rand(0, 9) < 8 ? 1 : 0;
                $clockIn = $worked ? rand(8, 10) . ':' . rand(0, 59) : null;
                $clockOut = $worked ? rand(17, 19) . ':' . rand(0, 59) : null;

                $attendance = Attendance::create([
                    'user_id' => $user->id,
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

                    if (rand(0, 1)) {
                        $attendance->breaks()->create([
                            'attendance_id' => $attendance->id,
                            'break_start' => rand(14, 15) . ':00',
                            'break_end' => rand(14, 15) . ':30',
                        ]);
                    }
                }
            }
        }
    }
}
