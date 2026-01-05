<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_name_is_in_detail_page()
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
            ->get('/attendance/detail/' . $attendance->id)
            ->assertSee($user->name);
    }
    public function test_detail_page_is_showing_choosen_day()
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
            ->get('/attendance/detail/' . $attendance->id)
            ->assertSee('2025年')
            ->assertSee('12月01日');
    }
    public function test_user_attendance_time_is_in_detail_page()
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
            ->get('/attendance/detail/' . $attendance->id)
            ->assertSee('09:00')
            ->assertSee('18:00');
    }
    public function test_user_break_time_is_in_detail_page()
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
            ->get('/attendance/detail/' . $attendance->id)
            ->assertSee('12:00')
            ->assertSee('13:00');
    }
}
