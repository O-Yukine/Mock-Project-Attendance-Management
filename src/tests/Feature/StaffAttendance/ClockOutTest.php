<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-01-01 10:30'));

        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-01-01',
            'clock_in' => '10:30',
            'status' => 'working',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '16:30',
            'action' => 'finish_working'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤済');
    }

    public function test_user_can_see_clock_out_time_on_attendance_list()
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
            ->get('/attendance')
            ->assertSee('退勤');


        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '18:30',
            'action' => 'finish_working'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤済');

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee('2026/01')
            ->assertSee('01/01(木)')
            ->assertSee('10:30')
            ->assertSee('18:30');
    }
}
