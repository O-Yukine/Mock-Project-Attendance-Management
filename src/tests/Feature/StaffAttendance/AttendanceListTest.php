<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_attendance_log_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $user = User::factory()->create();

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => 'clock_out',
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-02',
            'clock_in' => '10:30',
            'clock_out' => '19:30',
            'status' => 'clock_out',
        ]);

        $attendances = collect([$attendance1, $attendance2]);

        foreach ($attendances as $attendance) {

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);
        }

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2025/12')
            ->assertSee('12/01')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('12/02')
            ->assertSee('10:30')
            ->assertSee('19:30');
    }

    public function test_current_month_is_showing_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2026/01');
    }


    public function test_user_can_see_last_month_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2026/01')
            ->assertSee('前月');

        $this->get('/attendance/list?month=2025/12')
            ->assertSee('2025/12');
    }

    public function test_user_can_see_next_month_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2026/01')
            ->assertSee('翌月');

        $this->get('/attendance/list?month=2026/02')
            ->assertSee('2026/02');
    }

    public function test_user_can_access_to_attendance_detail()
    {

        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => 'clock_out',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
        ]);


        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2025/12')
            ->assertSee('12/01')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('詳細');

        $this->actingAs($user)
            ->get('/attendance/detail/' . $attendance->id)
            ->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('2025年')
            ->assertSee('12月01日')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('12:00')
            ->assertSee('13:00');
    }
}
