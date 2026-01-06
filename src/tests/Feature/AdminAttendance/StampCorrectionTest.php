<?php

namespace Tests\Feature\AdminAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Admin;
use Carbon\Carbon;

class StampCorrectionTest extends TestCase
{

    use RefreshDatabase;

    public function test_admin_can_access_to_attendance_detail()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

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

    public function test_admin_stamp_correction_clock_in_is_later_than_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => 'clock_out',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->patch('/admin/attendance/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '18:00',
                'clock_out' => '09:00',
                'breaks' => [
                    [
                        'break_start' => '12:30',
                        'break_end'   => '13:30',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_admin_stamp_correction_break_start_after_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

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
            ->patch('/admin/attendance/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'break_start' => '18:30',
                        'break_end'   => '19:30',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $response->assertSessionHasErrors(['breaks.0.break_start' => '休憩時間が不適切な値です']);
    }

    public function test_admin_stamp_correction_break_end_after_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

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
            ->patch('/admin/attendance/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'break_start' => '12:30',
                        'break_end'   => '18:30',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $response->assertSessionHasErrors(['breaks.0.break_end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }


    public function test_admin_stamp_correction_reason_is_empty()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => 'clock_out',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->patch('/admin/attendance/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'break_start' => '12:30',
                        'break_end'   => '13:30',
                    ],
                ],
                'reason' => ''
            ]);

        $response->assertSessionHasErrors(['reason' => '備考を記入してください']);
    }
}
