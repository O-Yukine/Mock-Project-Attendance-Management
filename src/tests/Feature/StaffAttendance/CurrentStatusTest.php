<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class CurrentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_not_started_can_see_off_status()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('勤務外');
    }

    public function test_user_working_can_see_status()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'status' => 'working',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤中');
    }

    public function test_user_on_break_can_see_status()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'status' => 'on_break',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩中');
    }

    public function test_user_finished_working_can_see_status()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'status' => 'clock_out',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤済');
    }
}
