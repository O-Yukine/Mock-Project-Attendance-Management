<?php

namespace Tests\Feature\AdminAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Admin;
use App\Models\BreakTimeLog;
use Carbon\Carbon;

class StampCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_pending_requests_on_the_stamp_correction_pending_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => '2025-12-01',
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'status' => 'clock_out',
            ]);

            $breakTime = BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);

            $attendanceLog = AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2025-12-01',
                'clock_in' => '09:30',
                'clock_out' => '18:30',
                'status' => 'pending',
                'reason' => '電車遅延',
                'requested_by' => 'user'
            ]);

            BreakTimeLog::create([
                'attendance_log_id' => $attendanceLog->id,
                'break_time_id' => $breakTime->id,
                'break_start' => '12:30',
                'break_end' => '13:30',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/list')
            ->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    public function test_admin_can_see_approved_requests_on_the_stamp_correction_approved_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => '2025-12-01',
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'status' => 'clock_out',
            ]);

            $breakTime = BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);

            $attendanceLog = AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2025-12-01',
                'clock_in' => '09:30',
                'clock_out' => '18:30',
                'status' => 'approved',
                'reason' => '電車遅延',
                'requested_by' => 'user'
            ]);

            BreakTimeLog::create([
                'attendance_log_id' => $attendanceLog->id,
                'break_time_id' => $breakTime->id,
                'break_start' => '12:30',
                'break_end' => '13:30',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/list?tab=approved')
            ->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    public function test_admin_can_view_pending_attendance_request_on_detail_page()
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

        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
        ]);

        $attendanceLog = AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'status' => 'pending',
            'reason' => '電車遅延のため',
            'requested_by' => 'user'
        ]);

        BreakTimeLog::create([
            'attendance_log_id' => $attendanceLog->id,
            'break_time_id' => $breakTime->id,
            'break_start' => '12:30',
            'break_end' => '13:30',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/list')
            ->assertSee($user->name)
            ->assertSee('詳細');

        $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/' . $attendance->id)
            ->assertSee($user->name)
            ->assertSee('2025年')
            ->assertSee('12月01日')
            ->assertSee('09:30')
            ->assertSee('18:30')
            ->assertSee('12:30')
            ->assertSee('13:30')
            ->assertSee('電車遅延のため');
    }

    public function test_admin_can_approve_request()
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

        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
        ]);

        $attendanceLog = AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'status' => 'pending',
            'reason' => '電車遅延のため',
            'requested_by' => 'user'
        ]);

        BreakTimeLog::create([
            'attendance_log_id' => $attendanceLog->id,
            'break_time_id' => $breakTime->id,
            'break_start' => '12:30',
            'break_end' => '13:30',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->patch('/stamp_correction_request/approve/' . $attendanceLog->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '09:30',
                'clock_out' => '18:30',
                'breaks' => [
                    [
                        'break_time_id' => $breakTime->id,
                        'break_start' => '12:30',
                        'break_end'   => '13:30',
                    ],
                ],
                'reason' => '電車遅延のため',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('attendance_logs', [
            'id' => $attendanceLog->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => Carbon::parse('09:30')->format('H:i:s'),
            'clock_out' => Carbon::parse('18:30')->format('H:i:s'),
        ]);

        $this->assertDatabaseMissing('break_times', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('H:i:s'),
        ]);

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:30')->format('H:i:s'),
            'break_end' => Carbon::parse('13:30')->format('H:i:s'),
        ]);
    }
}
