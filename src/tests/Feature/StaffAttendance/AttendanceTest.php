<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_in()
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 10:30'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('勤務外')
            ->assertSee('出勤');

        $response = $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '10:30',
            'action' => 'start_working'
        ]);

        $response->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤中');
    }

    public function test_user_can_clockIn_once_in_a_day()
    {

        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'status' => 'clock_out',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤済')
            ->assertDontSee('出勤');
    }

    public function test_user_can_see_clockIn_time_on_attendance_list()
    {

        Carbon::setTestNow(Carbon::parse('2026-01-01 10:30'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('勤務外')
            ->assertSee('出勤');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '10:30',
            'action' => 'start_working'
        ]);

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2026/01')
            ->assertSee('01/01(木)')
            ->assertSee('10:30');
    }
}
