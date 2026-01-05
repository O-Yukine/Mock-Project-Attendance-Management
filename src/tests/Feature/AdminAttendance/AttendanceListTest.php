<?php

namespace Tests\Feature\AdminAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_attendance_on_the_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-01'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => 'password']);
        $users = User::factory()->count(3)->create();

        $times = [
            ['09:00', '18:00'],
            ['10:00', '19:00'],
            ['11:00', '20:00'],
        ];

        foreach ($users as $index => $user) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => '2025/12/01',
                'clock_in'  => $times[$index][0],
                'clock_out' => $times[$index][1],
                'status' => 'clock_out'
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list');

        foreach ($users as $index => $user) {
            $response->assertSee($user->name)
                ->assertSee($times[$index][0])
                ->assertSee($times[$index][1]);
        }
    }

    public function test_current_day_is_showing_on_the_list()
    {

        Carbon::setTestNow(Carbon::parse('2025-12-01'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => 'password']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list')
            ->assertSee('2025/12/01');
    }

    public function test_user_can_see_yesterday_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-02'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => 'password']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list')
            ->assertSee('2025/12/02');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?day=2025/12/01')
            ->assertSee('2025/12/01');
    }

    public function test_user_can_see_tomorrow_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-02'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => 'password']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list')
            ->assertSee('2025/12/02');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?day=2025/12/03')
            ->assertSee('2025/12/03');
    }
}
