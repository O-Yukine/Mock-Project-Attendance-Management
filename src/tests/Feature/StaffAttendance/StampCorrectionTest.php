<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Admin;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class StampCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_stamp_correction_in_is_later_than_out()
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

        $response = $this->actingAs($user)
            ->post('/attendance/detail/' . $attendance->id, [
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

    public function test_user_stamp_correction_break_start_after_clock_out()
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

        $response = $this->actingAs($user)
            ->post('/attendance/detail/' . $attendance->id, [
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

    public function test_user_stamp_correction_break_end_after_clock_out()
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

        $response = $this->actingAs($user)
            ->post('/attendance/detail/' . $attendance->id, [
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


    public function test_user_stamp_correction_reason_is_empty()
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

        $response = $this->actingAs($user)
            ->post('/attendance/detail/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
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

    public function test_user_can_request_to_correct_attendance_detail()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $user = User::factory()->create();
        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

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
            ->post('/attendance/detail/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
                'clock_out' => '19:00',
                'breaks' => [
                    [
                        'break_start' => '13:00',
                        'break_end'   => '14:00',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $attendanceLog = AttendanceLog::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/list')
            ->assertSee($user->name)
            ->assertSee('承認待ち')
            ->assertSee('2025/12/01');

        $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/approve/' . $attendanceLog->id)
            ->assertSee($user->name)
            ->assertSee('電車遅延のため')
            ->assertSee('2025年')
            ->assertSee('12月01日');
    }

    public function test_user_pending_request_is_on_the_stamp_correction_pending_list()
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
            ->post('/attendance/detail/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
                'clock_out' => '19:00',
                'breaks' => [
                    [
                        'break_start' => '13:00',
                        'break_end'   => '14:00',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $this->get('/stamp_correction_request/list')
            ->assertSee($user->name)
            ->assertSee('承認待ち')
            ->assertSee('2025/12/01');
    }

    public function test_user_approved_request_is_on_the_stamp_correction_appreoved_list()
    {
        Carbon::setTestNow(Carbon::parse('2025-12-03'));

        $user = User::factory()->create();
        $admin = Admin::create(['email' => 'test@admin.com', 'password' => Hash::make('password'),]);

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
            ->post('/attendance/detail/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
                'clock_out' => '19:00',
                'breaks' => [
                    [
                        'break_start' => '13:00',
                        'break_end'   => '14:00',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $attendanceLog = AttendanceLog::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->patch('/stamp_correction_request/approve/' . $attendanceLog->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
                'clock_out' => '19:00',
                'breaks' => [
                    [
                        'break_start' => '13:00',
                        'break_end'   => '14:00',
                    ],
                ],
                'reason' => '電車遅延のため',
            ]);

        $this->actingAs($user)
            ->get('/stamp_correction_request/list?tab=approved')
            ->assertSee($user->name)
            ->assertSee('承認済み')
            ->assertSee('2025/12/01');
    }
    public function test_user_can_access_attendance_detail_from_stamp_correction_list()
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
        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
        ]);

        $this->actingAs($user)
            ->post('/attendance/detail/' . $attendance->id, [
                'work_date' => '2025-12-01',
                'clock_in' => '10:00',
                'clock_out' => '19:00',
                'breaks' => [
                    [
                        'break_start' => '13:00',
                        'break_end'   => '14:00',
                    ],
                ],
                'reason' => '電車遅延のため'
            ]);

        $this->get('/stamp_correction_request/list')
            ->assertSee($user->name)
            ->assertSee('承認待ち')
            ->assertSee('2025/12/01')
            ->assertSee('詳細');

        $this->get('/attendance/detail/' . $attendance->id)
            ->assertStatus(200);
    }
}
