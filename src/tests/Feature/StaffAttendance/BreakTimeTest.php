<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_go_on_break()
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
            ->assertSee('休憩入');

        $response = $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '12:30',
            'action' => 'break_start'
        ]);

        $response->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩中');
    }

    public function test_user_can_go_on_break_as_many_time()
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
            ->assertSee('休憩入');

        $response = $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '12:30',
            'action' => 'break_start'
        ]);

        $this->actingAs($user)
            ->get('/attendance');

        $response = $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '13:30',
            'action' => 'break_end'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩入');
    }

    public function test_user_can_come_back_from_break()
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
            ->assertSee('休憩入');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '12:30',
            'action' => 'break_start'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩戻');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '13:30',
            'action' => 'break_end'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤中');
    }

    public function test_user_can_come_back_from_break_as_many_time()
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
            ->assertSee('休憩入');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '12:30',
            'action' => 'break_start'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩戻');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '13:30',
            'action' => 'break_end'
        ]);

        $this->actingAs($user)
            ->get('/attendance');

        $this->post('/attendance', [
            'work_date' => '2026-01-01',
            'time' => '15:30',
            'action' => 'break_start'
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩戻');
    }

    // public function test_user_can_see_break_time_on_attendance_list()
    // {
    //     Carbon::setTestNow(Carbon::parse('2026-01-01 10:30'));

    //     $user = User::factory()->create();
    //     Attendance::create([
    //         'user_id' => $user->id,
    //         'work_date' => '2026-01-01',
    //         'clock_in' => '10:30',
    //         'status' => 'working',
    //     ]);

    //     $this->actingAs($user)
    //         ->get('/attendance')
    //         ->assertSee('休憩入');

    //     $this->post('/attendance', [
    //         'work_date' => '2026-01-01',
    //         'time' => '12:30',
    //         'action' => 'break_start'
    //     ]);

    //     $this->actingAs($user)
    //         ->get('/attendance')
    //         ->assertSee('休憩戻');

    //     $this->post('/attendance', [
    //         'work_date' => '2026-01-01',
    //         'time' => '13:30',
    //         'action' => 'break_end'
    //     ]);

    //     $this->actingAs($user)
    //         ->get('/attendance/list')
    //         ->assertSee('2026/01')
    //         ->assertSee('01/01(木)')
    //         ->assertSee('12:30', '13:30');
    // }
}
