<?php

namespace Tests\Feature\StaffAttendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;


class CurrentTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_current_time_and_date()
    {
        $user = User::factory()->create();
        Carbon::setTestNow(Carbon::parse('2026-01-01 10:30'));

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSeeText([
                '2026年1月1日',
                '10:30'
            ]);
    }
}
