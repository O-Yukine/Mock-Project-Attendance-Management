<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $users = User::factory()->count(5)->create();

        $startDate = now()->subMonth(2)->startOfDay();
        $endDate = today()->subDay()->startOfDay();

        $period = CarbonPeriod::create($startDate, $endDate);

        $shifts = [
            ['clock_in' => '06:00', 'clock_out' => '14:00', 'breaks' => [['10:00', '11:00']]],
            ['clock_in' => '14:00', 'clock_out' => '22:00', 'breaks' => [['17:00', '17:30'], ['20:00', '20:30']]],
            ['clock_in' => '10:00', 'clock_out' => '16:00', 'breaks' => [['12:00',  '12:30']]],
        ];
        foreach ($users as $user) {
            foreach ($period as $day) {

                if (rand(1, 10) > 8) {
                    Attendance::create([
                        'user_id'   => $user->id,
                        'work_date' => $day->toDateString(),
                        'status'    => 'off',
                    ]);
                    continue;
                }

                $shift = $shifts[array_rand($shifts)];

                $timeIn = Carbon::createFromFormat('H:i', $shift['clock_in'])
                    ->addMinutes([0, 15, 30, 45][array_rand([0, 1, 2, 3])]);

                $clockIn = $timeIn->format('H:i');
                $clockOut = $shift['clock_out'];

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $day->toDateString(),
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'status' => 'clock_out'
                ]);

                foreach ($shift['breaks'] as [$breakStart, $breakEnd]) {

                    $attendance->breaks()->create(
                        [
                            'break_start' => $breakStart,
                            'break_end' => $breakEnd
                        ],
                    );
                }
            }
        }
        //     $startDate = now()->subMonth(2)->startOfDay();
        //     $endDate = now()->subDay()->startOfDay();


        //     foreach ($users as $user) {
        //         for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {

        //             $worked = rand(1, 10) <= 8;
        //             $clockIn = $worked ? rand(8, 10) . ':' . rand(0, 59) : null;
        //             $clockOut = $worked ? rand(17, 19) . ':' . rand(0, 59) : null;

        //             $attendance = Attendance::create([
        //                 'user_id' => $user->id,
        //                 'work_date' => $day->toDateString(),
        //                 'clock_in' => $clockIn,
        //                 'clock_out' => $clockOut,
        //                 'status' => $worked ? 'clock_out' : 'off'
        //             ]);

        //             if ($worked) {
        //                 $attendance->breaks()->create([
        //                     'attendance_id' => $attendance->id,
        //                     'break_start' => rand(11, 12) . ':00',
        //                     'break_end' => rand(12, 13) . ':30',
        //                 ]);

        //                 if (rand(0, 1)) {
        //                     $attendance->breaks()->create([
        //                         'attendance_id' => $attendance->id,
        //                         'break_start' => rand(14, 15) . ':00',
        //                         'break_end' => rand(15, 16) . ':30',
        //                     ]);
        //                 }
        //             }
        //         }
        //     }
    }
}
