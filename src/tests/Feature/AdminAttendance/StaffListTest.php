<?php

namespace Tests\Feature\AdminAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;
use App\Models\BreakTime;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;


class StaffListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access__to_all_staff_information()
    {
        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/staff/list')
            ->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name)
                ->assertSee($user->email);
        }
    }

    public function test_admin_can_access_to_correct_staff_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-04'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

        $user = User::factory()->create();

        foreach (['2025-12-01', '2025-12-02', '2025-12-03'] as $date) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'status' => 'clock_out',
            ]);
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id)
            ->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('2025/12');

        foreach (['01', '02', '03'] as $day) {
            $response->assertSee('2025/12')
                ->assertSee("12/{$day}")
                ->assertSee('09:00')
                ->assertSee('18:00')
                ->assertSee('詳細');
        }
    }

    public function test_user_can_see_last_month_on_staff_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-04'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);
        $user = User::factory()->create();

        foreach (['2025-11-01', '2025-12-01', '2026-01-01'] as $date) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'status' => 'clock_out',
            ]);
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);
        }
        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id)
            ->assertSee('2025/12')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('詳細');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id . '?month=2025/11')
            ->assertSee('2025/11')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('詳細');
    }

    public function test_user_can_see_next_month_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-02'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);
        $user = User::factory()->create();

        foreach (['2025-12-01', '2026-01-01'] as $date) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'status' => 'clock_out',
            ]);
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);
        }
        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id)
            ->assertSee('2025/12')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('詳細');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id . '?month=2026/01')
            ->assertSee('2026/01')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('詳細');
    }
    public function test_admin_can_access_to_attendance_detail()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-01'));
        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

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

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id)
            ->assertStatus(200)
            ->assertSee('詳細');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/' . $attendance->id)
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
